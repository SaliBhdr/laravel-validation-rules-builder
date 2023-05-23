<?php

namespace SaliBhdr\ValidationRules\Facades;

use Illuminate\Support\Facades\Facade;
use SaliBhdr\ValidationRules\Contracts\RulesManagerContract;

/**
 * @method static                                                  self build()                                                     returns the instance of rules manager
 * @method self                                                    any(array $rules, bool $cacheable = true, array $binds = [])
 * @method self                                                    get(array $rules, bool $cacheable = true, array $binds = [])
 * @method self                                                    head(array $rules, bool $cacheable = true, array $binds = [])
 * @method self                                                    post(array $rules, bool $cacheable = true, array $binds = [])
 * @method self                                                    put(array $rules, bool $cacheable = true, array $binds = [])
 * @method self                                                    delete(array $rules, bool $cacheable = true, array $binds = [])
 * @method self                                                    connect(array $rules, bool $cacheable = true, array $binds = [])
 * @method self                                                    options(array $rules, bool $cacheable = true, array $binds = [])
 * @method self                                                    patch(array $rules, bool $cacheable = true, array $binds = [])
 * @method self                                                    purge(array $rules, bool $cacheable = true, array $binds = [])
 * @method self                                                    trace(array $rules, bool $cacheable = true, array $binds = [])
 * @method self                                                    create(array $rules, bool $cacheable = true, array $binds = [])
 * @method self                                                    update(array $rules, bool $cacheable = true, array $binds = [])
 * @method self                                                    cache(string $key = null, bool $force = false)                   enables caching on rules even if it is false in config
 * @method \SaliBhdr\ValidationRules\Contracts\CacheContract       getCache()                                                       returns cache instance
 * @method \SaliBhdr\ValidationRules\Contracts\CacheConfigContract getCacheConfig()                                                 returns cache config instance
 * @method bool                                                    isCached()                                                       shows that if the retrieved rules are the cached rules or just newly resolved
 * @method array                                                   rules(string $method = null,bool $override = false)              returns the resolved rules
 */
class RulesBuilder extends Facade
{
    /**
     * Indicates if the resolved instance should be cached.
     *
     * @var bool
     */
    protected static $cached = false;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return RulesManagerContract::class;
    }
}
