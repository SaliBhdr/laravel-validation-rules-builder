<?php

namespace SaliBhdr\ValidationRules\Contracts;

interface CacheContract
{
    /**
     * @param string $method
     * @param $value
     *
     * @return bool
     */
    public function put(string $method, $value): bool;

    /**
     * @param string $method
     *
     * @return array|null
     */
    public function get(string $method): ?array;

    /**
     * @param string $method
     *
     * @return bool
     */
    public function has(string $method): bool;

    /**
     * @return array
     */
    public function all(): array;

    /**
     * @return bool
     */
    public function flush(): bool;

    /**
     * @return CacheConfigContract
     */
    public function getConfig(): CacheConfigContract;
}
