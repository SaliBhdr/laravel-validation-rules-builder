<?php

namespace SaliBhdr\ValidationRules\Contracts;

interface CacheConfigContract
{
    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @param  bool  $isEnabled
     *
     * @return CacheConfigContract
     */
    public function enable(bool $isEnabled): CacheConfigContract;

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @param  \Illuminate\Http\Request|string|int  $request
     *
     * @return CacheConfigContract
     */
    public function setKey($prefix): CacheConfigContract;

    /**
     * @return CachePrefixContract
     */
    public function getKey(): CachePrefixContract;
}
