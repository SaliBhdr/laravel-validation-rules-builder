<?php

namespace SaliBhdr\ValidationRules\Cache;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use SaliBhdr\ValidationRules\Contracts\CachePrefixContract;

class CachePrefixFactory
{
    /**
     * @param  Request|string  $request
     *
     * @return CachePrefixContract
     */
    public function createPrefix($request): CachePrefixContract
    {
        if (is_string($request)) {
            $payload = $this->getStringPrefix($request);
        } elseif ($request instanceof FormRequest) {
            $payload = $this->getFormRequestPrefix($request);
        } else {
            $payload = $this->getRoutePrefix($request);
        }

        return $payload;
    }

    /**
     * @param  string  $request
     *
     * @return CachePrefix
     */
    protected function getStringPrefix(string $request): CachePrefix
    {
        if (class_exists($request)) {
            $payload = new CachePrefix('class', $request);
        } else {
            $payload = new CachePrefix('custom', $request);
        }

        return $payload;
    }

    /**
     * @param  FormRequest  $request
     *
     * @return CachePrefix
     */
    public function getFormRequestPrefix(FormRequest $request): CachePrefix
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

        if (!empty($route->getName())) {
            $payload = new CachePrefix('name', $route->getName());
        } else {
            $payload = new CachePrefix('uri', $route->uri());
        }

        return $payload;
    }
}
