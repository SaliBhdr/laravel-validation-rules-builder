<?php

namespace SaliBhdr\ValidationRules\Contracts;

interface RulesManagerContract
{
    /**
     * @return RulesManagerContract
     */
    public function build(): RulesManagerContract;

    /**
     * @return CacheContract
     */
    public function getCache(): CacheContract;

    /**
     * @return CacheConfigContract
     */
    public function getCacheConfig(): CacheConfigContract;

    /**
     * @return bool
     */
    public function isCached(): bool;

    /**
     * @return array
     */
    public function rules(): array;
}
