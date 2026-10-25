<?php

namespace Pharaonic\Laravel\Executor;

use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Pharaonic\Laravel\Executor\Classes\ExecutorManager;
use Pharaonic\Laravel\Executor\Console\ExecuteCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteFreshCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteMakeCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteRollbackCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteStatusCommand;
use Pharaonic\Laravel\Executor\Console\MakeExecuteCommand;
use Pharaonic\Laravel\Executor\Console\MakeExecutorCommand;

class ExecutorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/executor.php', 'pharaonic.executor');

        $this->app->singleton('pharaonic.executor.manager', ExecutorManager::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            AboutCommand::add('Pharaonic', [
                'Executor' => InstalledVersions::getPrettyVersion('pharaonic/laravel-executor') ?? 'unknown',
            ]);

            // `config` and `migrations` are the 1.x tags, kept for compatibility.
            $this->publishes([
                __DIR__.'/../config/executor.php' => $this->app->configPath('pharaonic/executor.php'),
            ], ['pharaonic', 'laravel-executor', 'pharaonic-config', 'executor-config', 'config']);

            $this->publishes([
                __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
            ], ['pharaonic', 'laravel-executor', 'pharaonic-migrations', 'executor-migrations', 'migrations']);

            $this->commands([
                MakeExecutorCommand::class,
                ExecuteMakeCommand::class,
                MakeExecuteCommand::class,
                ExecuteCommand::class,
                ExecuteRollbackCommand::class,
                ExecuteFreshCommand::class,
                ExecuteStatusCommand::class,
            ]);
        }
    }
}
