<?php

namespace SaliBhdr\ValidationRules\Cache;

use Illuminate\Http\Request;
use Illuminate\Config\Repository as Config;
use SaliBhdr\ValidationRules\Contracts\CacheConfigContract;
use SaliBhdr\ValidationRules\Contracts\CachePrefixContract;

class CacheConfig implements CacheConfigContract
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var CachePrefixContract
     */
    protected $prefix;

    /**
     * @var bool
     */
    protected $isEnabled;

    /**
     * @var CachePrefixFactory
     */
    protected $prefixFactory;

    public function __construct(Request $request, Config $config, CachePrefixFactory $prefixFactory)
    {
        $this->prefixFactory = $prefixFactory;
        $this->path          = $config->get('rules.cache.path');
        $this->isEnabled     = $config->get('rules.cache.enable');
        $this->prefix        = $this->prefixFactory->createPrefix($request);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param  bool  $isEnabled
     *
     * @return CacheConfigContract
     */
    public function enable(bool $isEnabled): CacheConfigContract
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @param  \Illuminate\Http\Request|string|int  $prefix
     *
     * @return CacheConfigContract
     */
    public function setKey($prefix): CacheConfigContract
    {
        $this->prefix = $this->prefixFactory->createPrefix($prefix);

        return $this;
    }

    /**
     * @return CachePrefixContract
     */
    public function getKey(): CachePrefixContract
    {
        return $this->prefix;
    }
}
