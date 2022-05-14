<?php

namespace SaliBhdr\ValidationRules;

use Illuminate\Http\Request;
use Illuminate\Support\Traits\ForwardsCalls;
use SaliBhdr\ValidationRules\Cache\CachePrefixFactory;
use SaliBhdr\ValidationRules\Contracts\CacheContract;
use Illuminate\Contracts\Config\Repository as Config;
use SaliBhdr\ValidationRules\Contracts\RulesBagContract;
use SaliBhdr\ValidationRules\Contracts\RulesManagerContract;

/**
 * @method self any(array $rules, array $binds = [], bool $override = false)
 * @method self get(array $rules, array $binds = [], bool $override = false)
 * @method self head(array $rules, array $binds = [], bool $override = false)
 * @method self post(array $rules, array $binds = [], bool $override = false)
 * @method self put(array $rules, array $binds = [], bool $override = false)
 * @method self delete(array $rules, array $binds = [], bool $override = false)
 * @method self connect(array $rules, array $binds = [], bool $override = false)
 * @method self options(array $rules, array $binds = [], bool $override = false)
 * @method self patch(array $rules, array $binds = [], bool $override = false)
 * @method self purge(array $rules, array $binds = [], bool $override = false)
 * @method self trace(array $rules, array $binds = [], bool $override = false)
 * @method self create(array $rules, array $binds = [], bool $override = false)
 * @method self update(array $rules, array $binds = [], bool $override = false)
 */
class RulesManager implements RulesManagerContract
{
    use ForwardsCalls;

    /**
     * @var string
     */
    protected $method = Methods::ANY;

    /**
     * @var RulesBagContract
     */
    protected $rulesBag;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var CacheContract
     */
    protected $cache;

    /**
     * @var bool
     */
    protected $isCached = false;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CachePrefixFactory
     */
    private $cachePrefixFactory;

    /**
     * @param  Request  $request
     * @param  RulesBagContract  $rulesBag
     * @param  CacheContract  $cache
     * @param  Config  $config
     * @param  CachePrefixFactory  $cachePrefixFactory
     */
    public function __construct(
        Request $request,
        RulesBagContract $rulesBag,
        CacheContract $cache,
        Config $config,
        CachePrefixFactory $cachePrefixFactory
    ) {
        $this->rulesBag = $rulesBag;
        $this->request  = $request;
        $this->cache    = $cache;
        $this->config   = $config;
        $this->cachePrefixFactory = $cachePrefixFactory;

        if ($this->rulesBag->isRuleAllowed($request->method())) {
            $this->method = strtoupper($request->method());
        }
    }

    /**
     * returns the instance of rules manager
     *
     * @param  Request|null  $request
     *
     * @return RulesManagerContract
     */
    public function build(Request $request = null): RulesManagerContract
    {
        if (!empty($request)) {
            $this->request = $request;
        }

        return $this;
    }

    /**
     * returns the resolved rules
     *
     * @param  string|null  $method
     * @param  bool|null  $override
     *
     * @return array
     */
    public function rules(string $method = null, bool $override = null): array
    {
        $method = empty($method) ? $this->method : strtoupper($method);

        $override = is_null($override) ? $this->rulesBag->isOverride($method) : $override;

        $result = $this->cache->get($method);

        if (!is_null($result)) {
            // todo:: some rules can be different based on request find a way to not cache them and merge them after cache retrieve
            // example : Rules::unique('table')->ignore($this->resource) : resource could be different based on request param
            $this->isCached = true;

            return $result;
        }

        $this->isCached = false;

        $this->cache->put($method, $result = $this->calculateRules($method, $override));

        return $result;
    }

    /**
     * @param  string|null  $method
     * @param  bool|null  $override
     *
     * @return array
     */
    protected function calculateRules(string $method = null, bool $override = null): array
    {
        $anyRules = $this->rulesBag->getRule(Methods::ANY);

        if ($method == Methods::ANY) {
            return $anyRules;
        }

        if (!$this->rulesBag->isRuleAllowed($method)) {
            return $override ? [] : $anyRules;
        }

        $methodRules = $this->rulesBag->getRule($method);

        return $this->mergeRules(
            $this->getArrayKeys($anyRules, $methodRules),
            $anyRules,
            $methodRules,
            $override
        );
    }

    protected function mergeRules(array $keys, array $firstRules, array $secondRules, bool $override): array
    {
        $result = [];

        foreach ($keys as $key) {
            $rulesOfKeyForFirstRules = $this->getRelatedRulesOfKey($firstRules, $key);

            $rulesOfKeyForSecondRules = $this->getRelatedRulesOfKey($secondRules, $key);

            if (empty($rulesOfKeyForFirstRules) && empty($rulesOfKeyForSecondRules)) {
                continue;
            }

            if ($override) {
                if (!empty($rulesOfKeyForSecondRules)) {
                    $result[$key] = $rulesOfKeyForSecondRules;
                }
                continue;
            }

            $result[$key] = array_merge($rulesOfKeyForFirstRules, $rulesOfKeyForSecondRules);
        }

        return $result;
    }

    /**
     * @param  array  $rules
     * @param  string  $key
     *
     * @return array
     */
    protected function getRelatedRulesOfKey(array $rules, string $key): array
    {
        if (!array_key_exists($key, $rules)) {
            return [];
        }

        return $this->keyRulesToArray($rules[$key]);
    }

    /**
     * @param  string|array  $rules
     *
     * @return array
     */
    protected function keyRulesToArray($rules): array
    {
        if (!is_array($rules)) {
            $rules = explode('|', $rules);
        }

        return $rules;
    }

    /**
     * @param  array  ...$arrays
     *
     * @return array
     */
    protected function getArrayKeys(array ...$arrays): array
    {
        $keys = [];

        foreach ($arrays as $arr) {
            $keys = array_merge($keys, array_keys($arr));
        }

        return array_unique($keys);
    }

    /**
     * enables caching on rules
     *
     * @param  string|null  $key
     * @param  bool  $force
     *
     * @return RulesManagerContract
     */
    public function cache(string $key = null, bool $force = false): RulesManagerContract
    {
        if ($this->config->get('rules.cache.enable', true) || $force) {
            $this->cache->enable(true)
                        ->prefix($this->cachePrefixFactory->createPrefix(empty($key) ? $this->request : $key));
        } else {
            $this->cache->enable(false);
        }

        return $this;
    }

    /**
     * shows that if the retrieved rules are the cached rules or just newly resolved
     *
     * @return bool
     */
    public function isCached(): bool
    {
        return $this->isCached;
    }

    /**
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->forwardDecoratedCallTo($this->rulesBag, $method, $parameters);
    }
}
