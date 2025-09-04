<?php

namespace Pharaonic\Laravel\Executor;

use Illuminate\Support\ServiceProvider;
use Pharaonic\Laravel\Executor\Classes\ExecutorManager;
use Pharaonic\Laravel\Executor\Classes\ExecutorPoolClass;
use Pharaonic\Laravel\Executor\Console\ExecuteCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteFreshCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteMakeCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteRollbackCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteStatusCommand;

class ExecutorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->mergeConfigFrom(__DIR__ . '/../config/executor.php', 'pharaonic.executor');

        $this->app->singleton('pharaonic.executor.manager', ExecutorManager::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [__DIR__ . '/../database/migrations' => database_path('migrations')],
                ['pharaonic', 'migrations', 'laravel-executor']
            );

            $this->publishes(
                [__DIR__ . '/../config/executor.php' => config_path('pharaonic/executor.php')],
                ['pharaonic', 'config', 'laravel-executor']
            );

            // // Load Commands
            // $this->commands([
            //     ExecuteCommand::class,
            //     ExecuteMakeCommand::class,
            //     ExecuteRollbackCommand::class,
            //     ExecuteFreshCommand::class,
            //     ExecuteStatusCommand::class,
            // ]);
        }
    }
}
