<?php

namespace SaliBhdr\ValidationRules;

class CachePrefixPayload
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

    public function __toString(): string
    {
        return $this->type.':'.$this->key;
    }
}
