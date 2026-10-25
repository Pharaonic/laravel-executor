<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Pharaonic\Laravel\Executor\Classes\Executor;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;

it('defaults the type to always', function () {
    $executor = new class extends Executor
    {
        public function up(): void {}

        public function down(): void {}
    };

    expect($executor->type)->toBe(ExecutorType::ALWAYS);
});

it('rejects a type that is not an ExecutorType', function () {
    new class extends Executor
    {
        public $type = 1;

        public function up(): void {}

        public function down(): void {}
    };
})->throws(InvalidArgumentException::class, 'The type of the executor must be an instance of ExecutorType.');

it('rejects tags that are not an array', function () {
    new class extends Executor
    {
        public $tags = 'deploy';

        public function up(): void {}

        public function down(): void {}
    };
})->throws(InvalidArgumentException::class, 'The tags of the executor must be an array.');

it('rejects servers that are not an array', function () {
    new class extends Executor
    {
        public $servers = '10.0.0.1';

        public function up(): void {}

        public function down(): void {}
    };
})->throws(InvalidArgumentException::class, 'The servers of the executor must be an array of ips.');

it('runs an artisan command', function () {
    Artisan::command('executor:ping', fn () => 0);

    $executor = new class extends Executor
    {
        public int $exitCode = -1;

        public function up(): void
        {
            $this->exitCode = $this->command('executor:ping');
        }

        public function down(): void {}
    };

    $executor->up();

    expect($executor->exitCode)->toBe(0);
});

it('rejects a callable job', function () {
    $executor = new class extends Executor
    {
        public function up(): void
        {
            $this->job(function () {});
        }

        public function down(): void {}
    };

    $executor->up();
})->throws(InvalidArgumentException::class, 'The job must be a string or an object.');
