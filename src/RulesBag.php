<?php

namespace SaliBhdr\ValidationRules;

use SaliBhdr\ValidationRules\Contracts\RulesBagContract;

class RulesBag implements RulesBagContract
{
    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @param  string  $name
     *
     * @return array
     */
    public function getRule(string $name): array
    {
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
     * @param  array  $rules
     * @param  array  $binds
     *
     * @return $this
     */
    public function create(array $rules, array $binds = []): RulesBagContract
    {
        return $this->add(Methods::POST, $rules, $binds);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     *
     * @return $this
     */
    public function update(array $rules, array $binds = []): RulesBagContract
    {
        $binds[] = Methods::PATCH;

        return $this->add(Methods::PUT, $rules, $binds);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     *
     * @return $this
     */
    public function any(array $rules, array $binds = []): RulesBagContract
    {
        return $this->add(Methods::ANY, $rules, $binds);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     *
     * @return $this
     */
    public function get(array $rules, array $binds = []): RulesBagContract
    {
        $binds[] = Methods::HEAD;

        return $this->add(Methods::GET, $rules, $binds);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     *
     * @return $this
     */
    public function head(array $rules, array $binds = []): RulesBagContract
    {
        return $this->add(Methods::HEAD, $rules, $binds);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     *
     * @return $this
     */
    public function post(array $rules, array $binds = []): RulesBagContract
    {
        return $this->add(Methods::POST, $rules, $binds);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     *
     * @return $this
     */
    public function put(array $rules, array $binds = []): RulesBagContract
    {
        return $this->add(Methods::PUT, $rules, $binds);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     *
     * @return $this
     */
    public function delete(array $rules, array $binds = []): RulesBagContract
    {
        return $this->add(Methods::DELETE, $rules, $binds);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     *
     * @return $this
     */
    public function connect(array $rules, array $binds = []): RulesBagContract
    {
        return $this->add(Methods::CONNECT, $rules, $binds);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     *
     * @return $this
     */
    public function options(array $rules, array $binds = []): RulesBagContract
    {
        return $this->add(Methods::OPTIONS, $rules, $binds);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     *
     * @return $this
     */
    public function patch(array $rules, array $binds = []): RulesBagContract
    {
        return $this->add(Methods::PATCH, $rules, $binds);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     *
     * @return $this
     */
    public function purge(array $rules, array $binds = []): RulesBagContract
    {
        return $this->add(Methods::PURGE, $rules, $binds);
    }

    /**
     * @param  array  $rules
     * @param  array  $binds
     *
     * @return $this
     */
    public function trace(array $rules, array $binds = []): RulesBagContract
    {
        return $this->add(Methods::TRACE, $rules, $binds);
    }

    /**
     * @param  string  $method
     * @param  array  $rules
     * @param  array  $binds
     *
     * @return $this
     */
    protected function add(string $method, array $rules, array $binds = []): self
    {
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
    protected function mergeTargetMethodRules(string $method, array $rules)
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
    protected function setTargetMethodRules(string $method, array $rules)
    {
        $this->rules[$method] = $rules;
    }

    /**
     * @param  array  $firstRules
     * @param  array  $secondRules
     *
     * @return array
     */
    protected function mergeRules(array $firstRules, array $secondRules): array
    {
        return array_merge_recursive($firstRules, $secondRules);
    }
}
