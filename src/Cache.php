<?php

namespace SaliBhdr\ValidationRules;

use Illuminate\Filesystem\Filesystem;
use SaliBhdr\ValidationRules\Contracts\CacheContract;
use Illuminate\Contracts\Config\Repository as Config;
use SaliBhdr\ValidationRules\Contracts\CachePrefixContract;

class Cache implements CacheContract
{
    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var bool
     */
    protected $isEnabled = false;

    /**
     * @var self|null
     */
    public static $instance = null;

    /**
     * @var Filesystem
     */
    protected Filesystem $files;


    private function __construct(Filesystem $files, Config $config)
    {
        $this->files = $files;

        $this->setPath($config->get('rules.cache.path'));

        if ($this->files->exists($this->path) && empty($this->rules)) {
            $this->rules = unserialize($this->files->get($this->path));
        }
    }

    public static function init(Filesystem $files, Config $config): Cache
    {
        if (is_null(static::$instance)) {
            static::$instance = new static($files, $config);
        }

        return static::$instance;
    }

    /**
     * @param  string  $method
     * @param $value
     * @return bool
     */
    public function put(string $method, $value): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $this->rules[$this->getCacheKey($method)] = $value;

        $this->files->put($this->path, serialize($this->rules));

        return true;
    }

    /**
     * @param  string  $method
     * @return mixed|null
     */
    public function get(string $method): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $key = $this->getCacheKey($method);

        if (isset($this->rules[$key])) {
            return $this->rules[$key];
        }

        return null;
    }

    /**
     * @param  CachePrefixContract  $prefix
     * @return CacheContract
     */
    public function prefix(CachePrefixContract $prefix): CacheContract
    {
        $this->prefix = $prefix->getType() . ':' . $prefix->getKey();

        return $this;
    }

    /**
     * @param  bool  $isEnabled
     * @return CacheContract
     */
    public function enable(bool $isEnabled): CacheContract
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * @param  string  $path
     * @return CacheContract
     */
    public function setPath(string $path): CacheContract
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return bool
     */
    protected function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @param  string  $method
     * @return string
     */
    protected function getCacheKey(string $method): string
    {
        return $this->prefix . ':' . $method;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->rules;
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->files->delete($this->getPath());
    }
}
