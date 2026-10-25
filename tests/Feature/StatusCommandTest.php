<?php

declare(strict_types=1);

it('lists pending executors', function () {
    $this->writeExecutor('pending', "public \$type = ExecutorType::ONCE;\n    public \$tags = ['cache', 'search'];\n    public \$servers = ['10.0.0.1'];");

    $this->artisan('execute:status')
        ->expectsTable(
            ['Name', 'Type', 'Tags', 'Servers', 'Batch', 'Executed'],
            [['pending', 'Once', 'cache, search', '10.0.0.1', '<comment>N/A</comment>', '<comment>No</comment>']]
        )
        ->assertExitCode(0);
});

it('lists executed executors', function () {
    $this->writeExecutor('done');
    $this->artisan('execute');

    $this->artisan('execute:status')
        ->expectsTable(
            ['Name', 'Type', 'Tags', 'Servers', 'Batch', 'Executed'],
            [['done', 'Always', 'None', 'All', 1, '<info>Yes</info>']]
        )
        ->assertExitCode(0);
});
