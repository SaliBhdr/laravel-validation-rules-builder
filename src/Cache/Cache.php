<?php

namespace SaliBhdr\ValidationRules\Cache;

use SaliBhdr\ValidationRules\Contracts\CacheConfigContract;
use SaliBhdr\ValidationRules\Contracts\CacheContract;

class Cache implements CacheContract
{
    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var CacheConfigContract
     */
    protected $config;

    public function __construct(CacheConfigContract $config)
    {
        $this->config = $config;

        if (file_exists($this->config->getPath()) && empty($this->rules)) {
            $this->rules = unserialize(file_get_contents($this->config->getPath()));
        }
    }

    /**
     * @param string $method
     * @param $value
     *
     * @return bool
     */
    public function put(string $method, $value): bool
    {
        if (!$this->config->isEnabled()) {
            return false;
        }

        $this->rules[$this->getCacheKey($method)] = $value;

        file_put_contents($this->config->getPath(), serialize($this->rules));

        return true;
    }

    /**
     * @param string $method
     *
     * @return mixed|null
     */
    public function get(string $method): ?array
    {
        if (!$this->config->isEnabled()) {
            return null;
        }

        if ($this->has($method)) {
            return $this->rules[$this->getCacheKey($method)];
        }

        return null;
    }

    /**
     * @param string $method
     *
     * @return mixed|null
     */
    public function has(string $method): bool
    {
        return array_key_exists($this->getCacheKey($method), $this->rules);
    }

    /**
     * @param string $method
     *
     * @return string
     */
    protected function getCacheKey(string $method): string
    {
        return $this->config->getKey()->getType() .
            ':' . $this->config->getKey()->getName() .
            ':' . $method;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->rules;
    }

    /**
     * @return bool
     */
    public function flush(): bool
    {
        return unlink($this->config->getPath());
    }

    /**
     * @return CacheConfigContract
     */
    public function getConfig(): CacheConfigContract
    {
        return $this->config;
    }
}
