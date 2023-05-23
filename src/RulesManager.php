<?php

namespace SaliBhdr\ValidationRules;

use Illuminate\Http\Request;
use Illuminate\Support\Traits\ForwardsCalls;
use SaliBhdr\ValidationRules\Contracts\CacheConfigContract;
use SaliBhdr\ValidationRules\Contracts\CacheContract;
use SaliBhdr\ValidationRules\Contracts\RulesBagContract;
use SaliBhdr\ValidationRules\Contracts\RulesManagerContract;

/**
 * @method self any(array $rules, bool $cacheable = true, array $binds = [])
 * @method self get(array $rules, bool $cacheable = true, array $binds = [])
 * @method self head(array $rules, bool $cacheable = true, array $binds = [])
 * @method self post(array $rules, bool $cacheable = true, array $binds = [])
 * @method self put(array $rules, bool $cacheable = true, array $binds = [])
 * @method self delete(array $rules, bool $cacheable = true, array $binds = [])
 * @method self connect(array $rules, bool $cacheable = true, array $binds = [])
 * @method self options(array $rules, bool $cacheable = true, array $binds = [])
 * @method self patch(array $rules, bool $cacheable = true, array $binds = [])
 * @method self purge(array $rules, bool $cacheable = true, array $binds = [])
 * @method self trace(array $rules, bool $cacheable = true, array $binds = [])
 * @method self create(array $rules, bool $cacheable = true, array $binds = [])
 * @method self update(array $rules, bool $cacheable = true, array $binds = [])
 */
class RulesManager implements RulesManagerContract
{
    use ForwardsCalls;

    /**
     * @var RulesBagContract
     */
    protected $cacheableRulesBag;

    /**
     * @var RulesBagContract
     */
    private $unCacheableRulesBag;

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
     * @param Request          $request
     * @param RulesBagContract $cacheableRulesBag
     * @param RulesBagContract $unCacheableRulesBag
     * @param CacheContract    $cache
     */
    public function __construct(
        Request $request,
        RulesBagContract $cacheableRulesBag,
        RulesBagContract $unCacheableRulesBag,
        CacheContract $cache
    ) {
        $this->request             = $request;
        $this->cacheableRulesBag   = $cacheableRulesBag;
        $this->unCacheableRulesBag = $unCacheableRulesBag;
        $this->cache               = $cache;
    }

    /**
     * returns the instance of rules manager
     *
     * @return RulesManagerContract
     */
    public function build(): RulesManagerContract
    {
        return $this;
    }

    /**
     * returns the resolved rules
     *
     * @param string|null $method
     * @param bool|null   $override
     *
     * @return array
     */
    public function rules(string $method = null, bool $override = false): array
    {
        $method = empty($method) ? $this->request->method() : strtoupper($method);

        $unCacheableResult = $this->calculateRules($this->unCacheableRulesBag, $method, $override);

        $cacheableResult = $this->cache->get($method);

        if (!is_null($cacheableResult)) {
            $this->isCached = true;

            return array_merge_recursive($cacheableResult, $unCacheableResult);
        }

        $this->isCached = false;

        $this->cache->put($method, $cacheableResult = $this->calculateRules($this->cacheableRulesBag, $method, $override));

        return array_merge_recursive($cacheableResult, $unCacheableResult);
    }

    /**
     * @param RulesBagContract $rulesBag
     * @param string           $method
     * @param bool             $override
     *
     * @return array
     */
    protected function calculateRules(RulesBagContract $rulesBag, string $method, bool $override): array
    {
        $anyRules = $rulesBag->getRule(Methods::ANY);

        if ($method == Methods::ANY) {
            return $anyRules;
        }

        $methodRules = $rulesBag->getRule($method);

        return $this->mergeRules(
            $this->getArrayKeys($anyRules, $methodRules),
            $anyRules,
            $methodRules,
            $override
        );
    }

    protected function mergeRules(array $keys, array $firstRules, array $secondRules, bool $override): array
    {
        if ($override && empty($secondRules)) {
            return [];
        }

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
     * @param array  $rules
     * @param string $key
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
     * @param string|array $rules
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
     * @param array ...$arrays
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
     * @return CacheContract
     */
    public function getCache(): CacheContract
    {
        return $this->cache;
    }

    /**
     * @return CacheConfigContract
     */
    public function getCacheConfig(): CacheConfigContract
    {
        return $this->cache->getConfig();
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
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if (method_exists($this, $method)) {
            return $this->{$method}(...$args);
        } elseif (
            method_exists($this->cacheableRulesBag, $method)
            || method_exists($this->unCacheableRulesBag, $method)
        ) {
            $cacheable = $args[1] ?? true;
            unset($args[1]);

            if ($cacheable) {
                return $this->decoratedForwardCallTo($this->cacheableRulesBag, $method, $args);
            } else {
                return $this->decoratedForwardCallTo($this->unCacheableRulesBag, $method, $args);
            }
        }

        self::throwBadMethodCallException($method);
    }

    /**
     * Forward a method call to the given object, returning $this if the forwarded call returned itself.
     *
     * @param mixed  $object
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    protected function decoratedForwardCallTo($object, string $method, array $args)
    {
        $result = $this->forwardCallTo($object, $method, $args);

        if ($result === $object) {
            return $this;
        }

        return $result;
    }
}
