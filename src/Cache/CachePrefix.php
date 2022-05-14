<?php

namespace SaliBhdr\ValidationRules\Cache;

use SaliBhdr\ValidationRules\Contracts\CachePrefixContract;

class CachePrefix implements CachePrefixContract
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $key;

    public function __construct(string $type, string $key)
    {
        $this->type = $type;
        $this->key  = $key;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
