<?php

namespace SaliBhdr\ValidationRules;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Config\Repository as Config;
use SaliBhdr\ValidationRules\Contracts\CacheContract;
use SaliBhdr\ValidationRules\Commands\RuleListCommand;
use SaliBhdr\ValidationRules\Commands\RuleClearCommand;
use SaliBhdr\ValidationRules\Contracts\RulesBagContract;
use SaliBhdr\ValidationRules\Contracts\RulesManagerContract;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->setupConfig();

        $this->app->singleton(CacheContract::class, function (Application $app) {
            return Cache::init($app->make(Filesystem::class), $app->make(Config::class));
        });

        $this->app->bind(RulesBagContract::class, function () {
            return new RulesBag();
        });

        $this->app->bind(RulesManagerContract::class, function (Application $app) {
            return new RulesManager(
                $app->make('request'),
                $app->make(RulesBagContract::class),
                $app->make(CacheContract::class),
                $app->make(Config::class)
            );
        });

        $this->setupCommands();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
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
     *
     * @return void
     */
    protected function setupCommands(): void
    {
        $this->commands([
            RuleClearCommand::class,
            RuleListCommand::class,
        ]);
    }
}
