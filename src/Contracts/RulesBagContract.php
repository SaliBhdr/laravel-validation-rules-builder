<?php

namespace SaliBhdr\ValidationRules\Contracts;

interface RulesBagContract
{
    /**
     * @param  string  $name
     * @return array
     */
    public function getRule(string $name): array;

    /**
     * @return array
     */
    public function getRules(): array;

    /**
     * @return array
     */
    public function getOverrides(): array;

    /**
     * @param  string  $name
     * @return bool
     */
    public function isOverride(string $name): bool;

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function create(array $rules, array $binds = [], bool $override = false): RulesBagContract;

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function update(array $rules, array $binds = [], bool $override = false): RulesBagContract;

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function any(array $rules, array $binds = [], bool $override = false): RulesBagContract;

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function get(array $rules, array $binds = [], bool $override = false): RulesBagContract;

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function head(array $rules, array $binds = [], bool $override = false): RulesBagContract;

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function post(array $rules, array $binds = [], bool $override = false): RulesBagContract;

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function put(array $rules, array $binds = [], bool $override = false): RulesBagContract;

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function delete(array $rules, array $binds = [], bool $override = false): RulesBagContract;

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function connect(array $rules, array $binds = [], bool $override = false): RulesBagContract;

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function options(array $rules, array $binds = [], bool $override = false): RulesBagContract;

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function patch(array $rules, array $binds = [], bool $override = false): RulesBagContract;

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function purge(array $rules, array $binds = [], bool $override = false): RulesBagContract;

    /**
     * @param  array  $rules
     * @param  array  $binds
     * @param  bool  $override
     * @return $this
     */
    public function trace(array $rules, array $binds = [], bool $override = false): RulesBagContract;
}
