<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Pharaonic\Laravel\Executor\Models\Executor;

function serverMigration(): object
{
    return include __DIR__.'/../../database/migrations/2026_11_02_000001_add_server_to_executors_table.php';
}

it('adds the server column to a table from an earlier release and keeps its records', function () {
    Executor::query()->create(['type' => 2, 'name' => 'once', 'batch' => 1, 'executed' => 1]);

    serverMigration()->down();
    expect(Schema::hasColumn('executors', 'server'))->toBeFalse();

    serverMigration()->up();

    expect(Schema::hasColumn('executors', 'server'))->toBeTrue()
        ->and(Executor::query()->where('name', 'once')->value('executed'))->toBe(1)
        ->and(Executor::query()->value('server'))->toBeNull();
});

it('does nothing on a table that has the server column', function () {
    serverMigration()->up();

    expect(Schema::hasColumn('executors', 'server'))->toBeTrue();
});

it('keeps one record per executor and server', function () {
    Executor::query()->create(['type' => 2, 'name' => 'web', 'server' => '10.0.0.1', 'batch' => 1, 'executed' => 1]);

    expect(fn () => Executor::query()->create(['type' => 2, 'name' => 'web', 'server' => '10.0.0.1', 'batch' => 1, 'executed' => 1]))
        ->toThrow(Illuminate\Database\QueryException::class);
});
