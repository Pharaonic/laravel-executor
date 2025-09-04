<?php

namespace Pharaonic\Laravel\Executor\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pharaonic\Laravel\Executor\Models\Executor;
use Pharaonic\Laravel\Executor\Services\ExecutorService;

class ExecutorTest extends TestCase
{
    use RefreshDatabase;

    public function testMakeExecutor()
    {
        $this->artisan('execute:make', ['name' => 'testMakeExecutor'])
            ->assertExitCode(0);
    }

    public function testMakeOnceExecutor()
    {
        $this->artisan('execute:make', [
            'name' => 'testMakeOnceExecutor',
            '--once' => true,
        ])
            ->assertExitCode(0);
    }

    public function testMakeExecutorWithTag()
    {
        $this->artisan('execute:make', [
            'name' => 'testMakeExecutorWithTag',
            '--tag' => 'test',
        ])
            ->assertExitCode(0);
    }

    public function testMakeOnceExecutorWithTag()
    {
        $this->artisan('execute:make', [
            'name' => 'testMakeOnceExecutorWithTag',
            '--once' => true,
            '--tag' => 'test',
        ])
            ->assertExitCode(0);
    }

    public function testExecute()
    {
        $this->testMakeExecutor();
        $this->artisan('execute')->assertOk();
        $this->assertEquals(1, Executor::count());
    }

    public function testRollbackSuccess()
    {
        $this->testMakeExecutor();
        $this->artisan('execute')->assertOk();
        $this->artisan('execute:rollback')->assertOk();
        $this->assertEquals(0, Executor::count());
    }

    public function testRollbackFailed()
    {
        $this->artisan('execute:rollback')->assertFailed();
    }

    public function testFreshExecutors()
    {
        $this->testMakeExecutor();
        $this->artisan('execute:fresh')->assertOk();
        $this->assertEquals(1, Executor::count());
    }

    public function testStatusOfExecutors()
    {
        $this->testMakeExecutor();

        $executors = (new ExecutorService())->sync();

        $this->artisan('execute:status')
            ->assertOk()
            ->expectsTable(
                ['Name', 'Type', 'Tag', 'Batch', 'Executed'],
                $executors->map(function ($executor) {
                    return [
                        $executor['name'],
                        ucfirst($executor['type']->name),
                        $executor['tag'],
                        $executor['model']->batch,
                        $executor['model']->executed > 0 ? '<info>Yes</info>' : '<comment>No</comment>',
                    ];
                })
            );
    }
}
