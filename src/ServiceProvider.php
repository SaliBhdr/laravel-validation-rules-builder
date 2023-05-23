<?php

namespace SaliBhdr\ValidationRules;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use SaliBhdr\ValidationRules\Cache\Cache;
use SaliBhdr\ValidationRules\Cache\CacheConfig;
use SaliBhdr\ValidationRules\Cache\CachePrefixFactory;
use SaliBhdr\ValidationRules\Commands\RuleClearCommand;
use SaliBhdr\ValidationRules\Commands\RuleListCommand;
use SaliBhdr\ValidationRules\Contracts\CacheConfigContract;
use SaliBhdr\ValidationRules\Contracts\CacheContract;
use SaliBhdr\ValidationRules\Contracts\RulesBagContract;
use SaliBhdr\ValidationRules\Contracts\RulesManagerContract;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->setupConfig();

        $this->app->bind(CacheConfigContract::class, function (Application $app) {
            return new CacheConfig(
                $app->make('request'),
                $app->make(Config::class),
                new CachePrefixFactory()
            );
        });

        $this->app->bind(CacheContract::class, function (Application $app) {
            return new Cache($app->make(CacheConfigContract::class));
        });

        $this->app->bind(RulesBagContract::class, function () {
            return new RulesBag();
        });

        $this->app->bind(RulesManagerContract::class, function (Application $app) {
            return new RulesManager(
                $app->make('request'),
                $app->make(RulesBagContract::class),
                $app->make(RulesBagContract::class),
                $app->make(CacheContract::class)
            );
        });

        $this->setupCommands();
    }

    /**
     * Setup the config.
     */
    protected function setupConfig(): void
    {
        $this->publishes([$this->getConfigFile() => config_path('rules.php')], 'rules');

        $this->mergeConfigFrom($this->getConfigFile(), 'rules');
    }

    /**
     * gets config file
     *
     * @return string
     */
    protected function getConfigFile(): string
    {
        return __DIR__ . '/../config/rules.php';
    }

    /**
     * add commands.
     */
    protected function setupCommands(): void
    {
        $this->commands([
            RuleClearCommand::class,
            RuleListCommand::class,
        ]);
    }
}
