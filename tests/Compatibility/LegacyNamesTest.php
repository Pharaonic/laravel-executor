<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Pharaonic\Laravel\Executor\ExecutorServiceProvider;

// Names from 1.x, 11.x and 12.x that keep working.
// ExecutorType::Always and ExecutorType::Once were removed in 10.0.0; see ExecutorTypeTest.

it('still creates an executor with the deprecated commands', function (string $command) {
    $this->artisan($command, ['name' => 'ClearCache'])
        ->expectsOutput("The {$command} command is deprecated. Use make:executor instead.")
        ->assertExitCode(0);

    expect(File::files(base_path('executors')))->toHaveCount(1)
        ->and(Artisan::all()[$command]->isHidden())->toBeTrue();
})->with(['execute:make', 'make:execute']);

it('still publishes with the generic tags', function () {
    expect(ServiceProvider::pathsToPublish(ExecutorServiceProvider::class, 'config'))
        ->toContain($this->app->configPath('pharaonic/executor.php'))
        ->and(ServiceProvider::pathsToPublish(ExecutorServiceProvider::class, 'migrations'))
        ->toContain($this->app->databasePath('migrations'));
});
