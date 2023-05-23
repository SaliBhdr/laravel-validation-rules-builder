<?php

namespace SaliBhdr\ValidationRules\Contracts;

interface RulesBagContract
{
    /**
     * @param string $name
     *
     * @return array
     */
    public function getRule(string $name): array;

    /**
     * @return array
     */
    public function getRules(): array;

    /**
     * @param array $rules
     * @param array $binds
     *
     * @return $this
     */
    public function create(array $rules, array $binds = []): RulesBagContract;

    /**
     * @param array $rules
     * @param array $binds
     *
     * @return $this
     */
    public function update(array $rules, array $binds = []): RulesBagContract;

    /**
     * @param array $rules
     * @param array $binds
     *
     * @return $this
     */
    public function any(array $rules, array $binds = []): RulesBagContract;

    /**
     * @param array $rules
     * @param array $binds
     *
     * @return $this
     */
    public function get(array $rules, array $binds = []): RulesBagContract;

    /**
     * @param array $rules
     * @param array $binds
     *
     * @return $this
     */
    public function head(array $rules, array $binds = []): RulesBagContract;

    /**
     * @param array $rules
     * @param array $binds
     *
     * @return $this
     */
    public function post(array $rules, array $binds = []): RulesBagContract;

    /**
     * @param array $rules
     * @param array $binds
     *
     * @return $this
     */
    public function put(array $rules, array $binds = []): RulesBagContract;

    /**
     * @param array $rules
     * @param array $binds
     *
     * @return $this
     */
    public function delete(array $rules, array $binds = []): RulesBagContract;

    /**
     * @param array $rules
     * @param array $binds
     *
     * @return $this
     */
    public function connect(array $rules, array $binds = []): RulesBagContract;

    /**
     * @param array $rules
     * @param array $binds
     *
     * @return $this
     */
    public function options(array $rules, array $binds = []): RulesBagContract;

    /**
     * @param array $rules
     * @param array $binds
     *
     * @return $this
     */
    public function patch(array $rules, array $binds = []): RulesBagContract;

    /**
     * @param array $rules
     * @param array $binds
     *
     * @return $this
     */
    public function purge(array $rules, array $binds = []): RulesBagContract;

    /**
     * @param array $rules
     * @param array $binds
     *
     * @return $this
     */
    public function trace(array $rules, array $binds = []): RulesBagContract;
}
