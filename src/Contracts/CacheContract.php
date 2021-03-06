<?php

namespace SaliBhdr\ValidationRules\Contracts;

interface CacheContract
{
    /**
     * @param  string  $method
     * @param $value
     * @return bool
     */
    public function put(string $method, $value): bool;

    /**
     * @param  string  $method
     * @return mixed|null
     */
    public function get(string $method): ?array;

    /**
     * @param  string  $prefix
     * @return CacheContract
     */
    public function prefix(string $prefix): CacheContract;

    /**
     * @param  bool  $isEnabled
     * @return CacheContract
     */
    public function enable(bool $isEnabled): CacheContract;

    /**
     * @param  string  $path
     * @return CacheContract
     */
    public function setPath(string $path): CacheContract;

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @return void
     */
    public function flush(): void;
}
