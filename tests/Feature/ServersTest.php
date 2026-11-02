<?php

declare(strict_types=1);

use Pharaonic\Laravel\Executor\Classes\ExecutorManager;
use Pharaonic\Laravel\Executor\Facades\Executor as ExecutorFacade;
use Pharaonic\Laravel\Executor\Models\Executor;
use Pharaonic\Laravel\Executor\Tests\Fixtures\Recorder;

/**
 * Make the next commands run as the server with these IP addresses.
 *
 * @param  array<int, string>  $ips
 */
function onServer(array $ips): void
{
    ExecutorFacade::swap(new class($ips) extends ExecutorManager
    {
        /**
         * @param  array<int, string>  $serverIps
         */
        public function __construct(private array $serverIps)
        {
            parent::__construct();
        }

        public function getIPs()
        {
            return $this->serverIps;
        }
    });
}

beforeEach(fn () => onServer(['10.0.0.1']));

it('runs an executor on one of its servers', function () {
    $this->writeExecutor('web', "public \$servers = ['10.0.0.1', '10.0.0.2'];");

    $this->artisan('execute');

    expect(Recorder::$calls)->toBe(['up:web']);
});

it('never runs an executor on another server', function () {
    $this->writeExecutor('worker', "public \$servers = ['10.0.0.9'];");

    $this->artisan('execute')->expectsOutput('There are no executors need to be executed.');

    expect(Recorder::$calls)->toBe([])
        ->and(Executor::query()->count())->toBe(0);
});

it('runs a once executor once on each of its servers', function () {
    $this->writeExecutor('web', "public \$type = ExecutorType::ONCE;\n    public \$servers = ['10.0.0.1', '10.0.0.2'];");

    $this->artisan('execute');
    $this->artisan('execute');

    onServer(['10.0.0.2']);
    $this->artisan('execute');
    $this->artisan('execute');

    expect(Recorder::$calls)->toBe(['up:web', 'up:web'])
        ->and(Executor::query()->orderBy('server')->pluck('server')->all())->toBe(['10.0.0.1', '10.0.0.2']);
});

it('runs an always executor on each of its servers every time', function () {
    $this->writeExecutor('web', "public \$type = ExecutorType::ALWAYS;\n    public \$servers = ['10.0.0.1', '10.0.0.2'];");

    $this->artisan('execute');
    $this->artisan('execute');

    onServer(['10.0.0.2']);
    $this->artisan('execute');

    expect(Recorder::$calls)->toBe(['up:web', 'up:web', 'up:web'])
        ->and(Executor::query()->where('server', '10.0.0.1')->value('executed'))->toBe(2)
        ->and(Executor::query()->where('server', '10.0.0.2')->value('executed'))->toBe(1);
});

it('keeps the record of the first listed address the server has', function () {
    onServer(['10.0.0.2', '10.0.0.1']);
    $this->writeExecutor('web', "public \$servers = ['10.0.0.1', '10.0.0.2'];");

    $this->artisan('execute');

    expect(Executor::query()->value('server'))->toBe('10.0.0.1');
});

it('runs a once executor without servers once for every server', function () {
    $this->writeExecutor('everywhere', 'public $type = ExecutorType::ONCE;');

    $this->artisan('execute');

    onServer(['10.0.0.2']);
    $this->artisan('execute');

    expect(Recorder::$calls)->toBe(['up:everywhere'])
        ->and(Executor::query()->value('server'))->toBeNull();
});

it('treats a record from before per-server records as run on every server', function () {
    $this->writeExecutor('web', "public \$type = ExecutorType::ONCE;\n    public \$servers = ['10.0.0.1', '10.0.0.2'];");
    Executor::query()->create(['type' => 2, 'name' => 'web', 'batch' => 1, 'executed' => 1]);

    $this->artisan('execute');

    onServer(['10.0.0.2']);
    $this->artisan('execute');

    expect(Recorder::$calls)->toBe([]);
});

it('rolls back only the records of this server', function () {
    $this->writeExecutor('web', "public \$type = ExecutorType::ONCE;\n    public \$servers = ['10.0.0.1', '10.0.0.2'];");

    $this->artisan('execute');
    onServer(['10.0.0.2']);
    $this->artisan('execute');

    Recorder::$calls = [];
    $this->artisan('execute:rollback');

    expect(Recorder::$calls)->toBe(['down:web'])
        ->and(Executor::query()->pluck('server')->all())->toBe(['10.0.0.1']);
});

it('never rolls back an executor on another server', function () {
    $this->writeExecutor('web', "public \$servers = ['10.0.0.1'];");
    $this->artisan('execute');

    onServer(['10.0.0.9']);
    Recorder::$calls = [];
    $this->artisan('execute:rollback')->expectsOutput('There are no executors has been found.');

    expect(Recorder::$calls)->toBe([])
        ->and(Executor::query()->count())->toBe(1);
});

it('stops running and rolling back on a server removed from the list', function () {
    $this->writeExecutor('web', "public \$type = ExecutorType::ONCE;\n    public \$servers = ['10.0.0.1', '10.0.0.2'];");
    $this->artisan('execute');
    onServer(['10.0.0.2']);
    $this->artisan('execute');

    $this->writeExecutor('web', "public \$type = ExecutorType::ONCE;\n    public \$servers = ['10.0.0.1'];");
    Recorder::$calls = [];

    $this->artisan('execute')->expectsOutput('There are no executors need to be executed.');
    $this->artisan('execute:rollback')->expectsOutput('There are no executors has been found.');

    onServer(['10.0.0.1']);
    $this->artisan('execute');

    expect(Recorder::$calls)->toBe([])
        ->and(Executor::query()->orderBy('server')->pluck('server')->all())->toBe(['10.0.0.1', '10.0.0.2']);
});

it('finds the record by any address of the server', function () {
    onServer(['10.0.0.1', '10.0.0.5']);
    $this->writeExecutor('web', "public \$type = ExecutorType::ONCE;\n    public \$servers = ['10.0.0.1', '10.0.0.5'];");
    $this->artisan('execute');

    $this->writeExecutor('web', "public \$type = ExecutorType::ONCE;\n    public \$servers = ['10.0.0.5'];");
    Recorder::$calls = [];
    $this->artisan('execute');

    expect(Recorder::$calls)->toBe([]);
});

it('runs a once executor whose servers were emptied once more, for the servers that never ran it', function () {
    $this->writeExecutor('web', "public \$type = ExecutorType::ONCE;\n    public \$servers = ['10.0.0.1'];");
    $this->artisan('execute');

    $this->writeExecutor('web', 'public $type = ExecutorType::ONCE;');
    Recorder::$calls = [];

    $this->artisan('execute');
    onServer(['10.0.0.3']);
    $this->artisan('execute');
    onServer(['10.0.0.4']);
    $this->artisan('execute');

    expect(Recorder::$calls)->toBe(['up:web'])
        ->and(Executor::query()->orderBy('server')->pluck('server')->all())->toBe([null, '10.0.0.1']);
});

it('runs an executor on the server with a listed name', function () {
    config(['pharaonic.executor.server' => 'web-1']);
    $this->writeExecutor('web', "public \$servers = ['web-1', 'web-2'];");
    $this->writeExecutor('worker', "public \$servers = ['worker-1'];");

    $this->artisan('execute');

    expect(Recorder::$calls)->toBe(['up:web'])
        ->and(Executor::query()->value('server'))->toBe('web-1');
});

it('keeps the record of a named server when its address changes', function () {
    config(['pharaonic.executor.server' => 'web-1']);
    $this->writeExecutor('web', "public \$type = ExecutorType::ONCE;\n    public \$servers = ['web-1'];");
    $this->artisan('execute');

    onServer(['172.17.0.9']);
    Recorder::$calls = [];
    $this->artisan('execute')->expectsOutput('There are no executors need to be executed.');

    expect(Recorder::$calls)->toBe([]);
});

it('mixes server names and addresses in one list', function () {
    $this->writeExecutor('web', "public \$type = ExecutorType::ONCE;\n    public \$servers = ['web-1', '10.0.0.1'];");

    $this->artisan('execute');

    config(['pharaonic.executor.server' => 'web-1']);
    onServer(['172.17.0.9']);
    $this->artisan('execute');

    expect(Recorder::$calls)->toBe(['up:web', 'up:web'])
        ->and(Executor::query()->orderBy('server')->pluck('server')->all())->toBe(['10.0.0.1', 'web-1']);
});

it('ignores an empty server name', function () {
    config(['pharaonic.executor.server' => '']);

    expect(ExecutorFacade::getServer())->toBeNull()
        ->and(ExecutorFacade::isServer(''))->toBeFalse();
});

it('stores the servers of the executor', function () {
    $this->writeExecutor('web', "public \$servers = ['10.0.0.1'];");
    $this->writeExecutor('everywhere');

    $this->artisan('execute');

    expect(Executor::query()->where('name', 'web')->firstOrFail()->servers)->toBe(['10.0.0.1'])
        ->and(Executor::query()->where('name', 'everywhere')->firstOrFail()->servers)->toBeNull();
});
