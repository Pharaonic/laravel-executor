<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;
use Pharaonic\Laravel\Executor\Models\Executor;
use Pharaonic\Laravel\Executor\Tests\Fixtures\Recorder;

function createTheOneXTable(): void
{
    Schema::dropIfExists('executors');
    Schema::create('executors', function (Blueprint $table) {
        $table->id();
        $table->unsignedTinyInteger('type')->default(1);
        $table->string('executor');
        $table->string('tag')->nullable();
        $table->integer('batch')->default(1);
        $table->integer('executed')->default(0);
        $table->timestamp('last_executed_at')->nullable();
    });

    DB::table('executors')->insert([
        ['type' => 2, 'executor' => 'once', 'tag' => 'cache', 'batch' => 1, 'executed' => 1],
        ['type' => 1, 'executor' => 'always', 'tag' => null, 'batch' => 2, 'executed' => 3],
    ]);
}

function upgradeFromOneX(): void
{
    (include __DIR__.'/../../database/migrations/2026_10_25_000001_upgrade_executors_table_from_1x.php')->up();
}

it('converts a 1.x table and keeps its records', function () {
    createTheOneXTable();

    upgradeFromOneX();

    expect(Schema::hasColumns('executors', ['name', 'tags', 'servers']))->toBeTrue()
        ->and(Schema::hasColumn('executors', 'executor'))->toBeFalse()
        ->and(Schema::hasColumn('executors', 'tag'))->toBeFalse();

    $once = Executor::query()->where('name', 'once')->firstOrFail();
    $always = Executor::query()->where('name', 'always')->firstOrFail();

    expect($once->type)->toBe(ExecutorType::ONCE)
        ->and($once->tags)->toBe(['cache'])
        ->and($once->executed)->toBe(1)
        ->and($always->tags)->toBeNull()
        ->and($always->batch)->toBe(2)
        ->and($always->executed)->toBe(3);
});

it('does not run a once executor again after the upgrade', function () {
    createTheOneXTable();
    upgradeFromOneX();

    $this->writeExecutor('once', 'public $type = ExecutorType::ONCE;');
    $this->writeExecutor('always');

    $this->artisan('execute')->assertExitCode(0);

    expect(Recorder::$calls)->toBe(['up:always'])
        ->and(Executor::query()->where('name', 'always')->value('executed'))->toBe(4);
});

it('leaves a current table untouched', function () {
    Executor::query()->create(['type' => ExecutorType::ONCE, 'name' => 'once', 'batch' => 1, 'executed' => 1]);

    upgradeFromOneX();

    expect(Executor::query()->where('name', 'once')->value('executed'))->toBe(1)
        ->and(Schema::hasColumn('executors', 'executor'))->toBeFalse();
});
