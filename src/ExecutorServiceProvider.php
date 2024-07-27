<?php

namespace Pharaonic\Laravel\Executor;

use Illuminate\Support\ServiceProvider;
use Pharaonic\Laravel\Executor\Console\ExecuteCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteFreshCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteMakeCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteRollbackCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteStatusCommand;
use Pharaonic\Laravel\Executor\ExecutorPool;

class ExecutorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        ExecutorPool::addPath(base_path('executors'));

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
