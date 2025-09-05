<?php

namespace Pharaonic\Laravel\Executor\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Pharaonic\Laravel\Executor\Models\Executor;

class ExecutorTest extends TestCase
{
    use RefreshDatabase;

    public function testMakeExecutor()
    {
        $this->artisan('execute:make', ['name' => 'testMakeExecutor'])
            ->assertExitCode(0);
    }

    public function testExecute()
    {
        $this->artisan('execute:make', ['name' => 'testMakeExecutor']);
        $this->artisan('execute')->assertOk();
        $this->assertEquals(1, Executor::count());
    }

    public function testRollbackSuccess()
    {
        $this->artisan('execute:make', ['name' => 'testMakeExecutor']);
        $this->artisan('execute')->assertOk();
        $this->artisan('execute:rollback')->assertOk();
        $this->assertEquals(0, Executor::count());
    }

    public function testRollbackFailed()
    {
        $this->artisan('execute:rollback')
            ->expectsOutput('There are no executors has been found.')
            ->assertExitCode(0);
    }

    public function testFreshExecutors()
    {
        $this->artisan('execute:make', ['name' => 'testMakeExecutor']);
        $this->artisan('execute:fresh')->assertOk();
        $this->assertEquals(1, Executor::count());
    }

    public function testStatusOfExecutors()
    {
        $this->artisan('execute:make', ['name' => 'testMakeExecutor']);
        $this->artisan('execute:status')
            ->assertOk()
            ->expectsTable(
                ['Name', 'Type', 'Tags', 'Servers', 'Batch', 'Executed'],
                [
                    [
                        basename(File::files(base_path('executors'))[0]->getBasename(), '.php'),
                        'Always',
                        'None',
                        'All',
                        '<comment>N/A</comment>',
                        '<comment>No</comment>',
                    ],
                ]
            );
    }
}
