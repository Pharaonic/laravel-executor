<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Pharaonic\Laravel\Executor\Classes\Executor;

it('creates an executor in the executors directory', function () {
    $this->artisan('make:executor', ['name' => 'ClearCache'])->assertExitCode(0);

    $files = File::files(base_path('executors'));

    expect($files)->toHaveCount(1)
        ->and($files[0]->getFilename())->toMatch('/^\d{4}_\d{2}_\d{2}_\d{6}_clear_cache\.php$/')
        ->and(include $files[0]->getRealPath())->toBeInstanceOf(Executor::class);
});

it('creates an executor in a custom path', function () {
    $this->artisan('make:executor', ['name' => 'ClearCache', '--path' => 'custom-executors'])->assertExitCode(0);

    expect(File::files(base_path('custom-executors')))->toHaveCount(1)
        ->and(File::isDirectory(base_path('executors')))->toBeFalse();
});

it('declares the canonical type in the stub', function () {
    $this->artisan('make:executor', ['name' => 'ClearCache']);

    expect(File::get(File::files(base_path('executors'))[0]->getRealPath()))
        ->toContain('public $type = ExecutorType::ALWAYS;');
});
