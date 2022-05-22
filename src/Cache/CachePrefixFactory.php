<?php

namespace SaliBhdr\ValidationRules\Cache;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use SaliBhdr\ValidationRules\Contracts\CachePrefixContract;

class CachePrefixFactory
{
    /**
     * @param  Request|string|int  $request
     *
     * @return CachePrefixContract
     */
    public function createPrefix($request): CachePrefixContract
    {
        $prefix = null;

        if (is_string($request) || is_numeric($request)) {
            $prefix = $this->getStringPrefix($request);
        } elseif ($request instanceof FormRequest) {
            $prefix = $this->getFormRequestPrefix($request);
        } elseif ($request instanceof Request) {
            $prefix = $this->getRoutePrefix($request);
        }

        return $prefix;
    }

    /**
     * @param  string  $request
     *
     * @return CachePrefix
     */
    protected function getStringPrefix(string $request): CachePrefix
    {
        if (class_exists($request)) {
            $prefix = new CachePrefix('class', $request);
        } else {
            $prefix = new CachePrefix('custom', $request);
        }

        return $prefix;
    }

    /**
     * @param  FormRequest  $request
     *
     * @return CachePrefix
     */
    protected function getFormRequestPrefix(FormRequest $request): CachePrefix
    {
        return new CachePrefix('form', get_class($request));
    }

    /**
     * @param  Request  $request
     *
     * @return CachePrefix
     */
    protected function getRoutePrefix(Request $request): CachePrefix
    {
        $route = $request->route();

        if (empty($route)) {
            return new CachePrefix('request', $request->getUri());
        }

        if (!empty($route->getName())) {
            return new CachePrefix('name', $route->getName());
        }

        return new CachePrefix('uri', $route->uri());
    }
}
