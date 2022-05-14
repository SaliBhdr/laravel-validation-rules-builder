<?php

namespace SaliBhdr\ValidationRules\Contracts;

interface CachePrefixContract
{
    public function __construct(string $type, string $key);

    public function getType(): string;

    public function getKey(): string;
}
