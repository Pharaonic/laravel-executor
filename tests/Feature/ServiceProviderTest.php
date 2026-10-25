<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Pharaonic\Laravel\Executor\Classes\ExecutorManager;
use Pharaonic\Laravel\Executor\ExecutorServiceProvider;
use Pharaonic\Laravel\Executor\Facades\Executor;

it('registers the service provider', function () {
    expect($this->app->getProviders(ExecutorServiceProvider::class))->not->toBeEmpty();
});

it('merges the package configuration', function () {
    expect(config('pharaonic.executor'))->toBeArray()
        ->and(config('pharaonic.executor.table'))->toBe('executors');
});

it('binds the manager behind the facade', function () {
    expect(Executor::getFacadeRoot())->toBeInstanceOf(ExecutorManager::class)
        ->and(Executor::getFacadeRoot())->toBe($this->app->make('pharaonic.executor.manager'));
});

it('registers the commands', function (string $name) {
    $command = Artisan::all()[$name] ?? null;

    expect($command)->not->toBeNull()
        ->and($command?->isHidden())->toBeFalse();
})->with(['make:executor', 'execute', 'execute:rollback', 'execute:fresh', 'execute:status']);

it('publishes the config', function (string $tag) {
    expect(ServiceProvider::pathsToPublish(ExecutorServiceProvider::class, $tag))
        ->toContain($this->app->configPath('pharaonic/executor.php'));
})->with(['pharaonic', 'laravel-executor', 'pharaonic-config', 'executor-config']);

it('publishes the migrations', function (string $tag) {
    expect(ServiceProvider::pathsToPublish(ExecutorServiceProvider::class, $tag))
        ->toContain($this->app->databasePath('migrations'));
})->with(['pharaonic', 'laravel-executor', 'pharaonic-migrations', 'executor-migrations']);

it('loads the migrations', function () {
    expect(Schema::hasTable('executors'))->toBeTrue();
});
