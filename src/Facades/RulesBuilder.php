<?php

namespace SaliBhdr\ValidationRules\Facades;

use Illuminate\Support\Facades\Facade;
use SaliBhdr\ValidationRules\Contracts\RulesManagerContract;

/**
 * @method static self build(\Illuminate\Http\Request $request = null) returns the instance of rules manager
 * @method self any(array $rules, array $binds = [], bool $override = false)
 * @method self get(array $rules, array $binds = [], bool $override = false)
 * @method self head(array $rules, array $binds = [], bool $override = false)
 * @method self post(array $rules, array $binds = [], bool $override = false)
 * @method self put(array $rules, array $binds = [], bool $override = false)
 * @method self delete(array $rules, array $binds = [], bool $override = false)
 * @method self connect(array $rules, array $binds = [], bool $override = false)
 * @method self options(array $rules, array $binds = [], bool $override = false)
 * @method self patch(array $rules, array $binds = [], bool $override = false)
 * @method self purge(array $rules, array $binds = [], bool $override = false)
 * @method self trace(array $rules, array $binds = [], bool $override = false)
 * @method self create(array $rules, array $binds = [], bool $override = false)
 * @method self update(array $rules, array $binds = [], bool $override = false)
 * @method self cache(string $key = null, bool $force = false) enables caching on rules
 * @method bool isCached() shows that if the retrieved rules are the cached rules or just newly resolved
 * @method array rules(string $method = null, bool $override = null) returns the resolved rules
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
