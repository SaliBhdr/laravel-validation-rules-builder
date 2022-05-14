<?php

namespace SaliBhdr\ValidationRules;

use SaliBhdr\ValidationRules\Contracts\RulesBagContract;

class RulesBag implements RulesBagContract
{
    /**
     * @var array[]
     */
    protected $rules = [
        Methods::ANY     => [],
        Methods::GET     => [],
        Methods::HEAD    => [],
        Methods::POST    => [],
        Methods::PUT     => [],
        Methods::DELETE  => [],
        Methods::CONNECT => [],
        Methods::OPTIONS => [],
        Methods::PATCH   => [],
        Methods::PURGE   => [],
        Methods::TRACE   => [],
    ];

    /**
     * @var array
     */
    protected $overrides = [];

    /**
     * @return array[]
     */
    public function getRule(string $name): array
    {
        $name = strtoupper($name);

        return $this->rules[$name] ?? [];
    }

    /**
     * @return array[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return array
     */
    public function getOverrides(): array
    {
        return $this->overrides;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function isOverride(string $name): bool
    {
        return in_array($name, $this->overrides);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function create(array $rules, array $binds = [], bool $override = false): RulesBagContract
    {
        return $this->add(Methods::POST, $rules, $binds, $override);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function update(array $rules, array $binds = [], bool $override = false): RulesBagContract
    {
        $binds[] = Methods::PATCH;

        return $this->add(Methods::PUT, $rules, $binds, $override);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function any(array $rules, array $binds = [], bool $override = false): RulesBagContract
    {
        return $this->add(Methods::ANY, $rules, $binds, $override);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function get(array $rules, array $binds = [], bool $override = false): RulesBagContract
    {
        $binds[] = Methods::HEAD;

        return $this->add(Methods::GET, $rules, $binds, $override);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function head(array $rules, array $binds = [], bool $override = false): RulesBagContract
    {
        return $this->add(Methods::HEAD, $rules, $binds, $override);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function post(array $rules, array $binds = [], bool $override = false): RulesBagContract
    {
        return $this->add(Methods::POST, $rules, $binds, $override);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function put(array $rules, array $binds = [], bool $override = false): RulesBagContract
    {
        return $this->add(Methods::PUT, $rules, $binds, $override);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function delete(array $rules, array $binds = [], bool $override = false): RulesBagContract
    {
        return $this->add(Methods::DELETE, $rules, $binds, $override);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function connect(array $rules, array $binds = [], bool $override = false): RulesBagContract
    {
        return $this->add(Methods::CONNECT, $rules, $binds, $override);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function options(array $rules, array $binds = [], bool $override = false): RulesBagContract
    {
        return $this->add(Methods::OPTIONS, $rules, $binds, $override);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function patch(array $rules, array $binds = [], bool $override = false): RulesBagContract
    {
        return $this->add(Methods::PATCH, $rules, $binds, $override);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function purge(array $rules, array $binds = [], bool $override = false): RulesBagContract
    {
        return $this->add(Methods::PURGE, $rules, $binds, $override);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function trace(array $rules, array $binds = [], bool $override = false): RulesBagContract
    {
        return $this->add(Methods::TRACE, $rules, $binds, $override);
    }

    /**
     * @param  string  $method
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    protected function add(string $method, array $rules, array $binds = [], bool $override = false): self
    {
        if ($override) {
            $this->overrides[] = $method;
        }

        $this->mergeTargetMethodRules($method, $rules);

        $binds = array_unique($binds);

        foreach ($binds as $bind) {
            $this->mergeTargetMethodRules($bind, $rules);
        }

        return $this;
    }

    /**
     * @param  string  $method
     * @param  array  $rules
     *
     * @return void
     */
    protected function mergeTargetMethodRules(string $method, array $rules): void
    {
        $this->setTargetMethodRules(
            $method,
            $this->mergeRules($this->getRule($method), $rules)
        );
    }

    /**
     * @param  string  $method
     * @param  array  $rules
     *
     * @return void
     */
    protected function setTargetMethodRules(string $method, array $rules): void
    {
        $method = strtoupper($method);

        if (!$this->isRuleAllowed($method)) {
            return;
        }

        $this->rules[$method] = $rules;
    }

    /**
     * @param  array  $firstRules
     * @param  array  $secondRules
     * @return array
     */
    protected function mergeRules(array $firstRules, array $secondRules): array
    {
        return array_merge_recursive($firstRules, $secondRules);
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function isRuleAllowed(string $name): bool
    {
        return array_key_exists($name, $this->rules);
    }
}
