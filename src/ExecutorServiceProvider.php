<?php

namespace Pharaonic\Laravel\Executor;

use Illuminate\Support\ServiceProvider;
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
        $this->app->singleton('pharaonic.executor.executorPool', ExecutorPoolClass::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // Publish Migrations
            $this->publishes(
                [__DIR__ . '/../database/migrations' => database_path('migrations')],
                ['pharaonic', 'migrations', 'executor-migrations']
            );

            // Load Migrations
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

            // Load Commands
            $this->commands([
                ExecuteCommand::class,
                ExecuteMakeCommand::class,
                ExecuteRollbackCommand::class,
                ExecuteFreshCommand::class,
                ExecuteStatusCommand::class,
            ]);
        }
    }
}
