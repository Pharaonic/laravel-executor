<?php

namespace Pharaonic\Laravel\Executor;

use Illuminate\Support\ServiceProvider;
use Pharaonic\Laravel\Executor\Console\ExecuteCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteFreshCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteMakeCommand;
use Pharaonic\Laravel\Executor\Console\ExecuteRollbackCommand;

class ExecutorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // 
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ExecuteCommand::class,
                ExecuteMakeCommand::class,
                ExecuteRollbackCommand::class,
                ExecuteFreshCommand::class,
            ]);
        }
    }
}
