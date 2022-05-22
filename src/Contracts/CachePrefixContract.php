<?php

namespace SaliBhdr\ValidationRules\Contracts;

interface CachePrefixContract
{
    /**
     * @param  string  $type
     * @param  string  $name
     */
    public function __construct(string $type, string $name);

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getName(): string;
}
