<?php

namespace SaliBhdr\ValidationRules\Contracts;

use Illuminate\Http\Request;

interface RulesManagerContract
{
    public function build(Request $request): RulesManagerContract;

    public function cache(string $key, bool $force): RulesManagerContract;

    public function isCached(): bool;

    public function rules(): array;
}
