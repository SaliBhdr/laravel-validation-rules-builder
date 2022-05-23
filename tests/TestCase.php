<?php

namespace SaliBhdr\ValidationRules\Tests;

use Illuminate\Foundation\Application;
use SaliBhdr\ValidationRules\ServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @param  Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }
}
