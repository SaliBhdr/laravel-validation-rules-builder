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
    protected $name;

    public function __construct(string $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;
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
    public function getName(): string
    {
        return $this->name;
    }
}
