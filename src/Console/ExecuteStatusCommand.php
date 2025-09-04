<?php

namespace Pharaonic\Laravel\Executor\Console;

use Illuminate\Console\Command;
use Pharaonic\Laravel\Executor\Facades\Executor;

class ExecuteStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the status of the executors.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->table(
            ['Name', 'Type', 'Tags', 'Servers', 'Batch', 'Executed'],
            collect(Executor::info())->map(function ($executor) {
                return [
                    $executor['name'],
                    $executor['type']->name,
                    empty($executor['tags']) ? 'None' : implode(', ', $executor['tags']),
                    empty($executor['servers']) ? 'All' : implode(', ', $executor['servers']),
                    $executor['batch'] ?? '<comment>N/A</comment>',
                    $executor['executed'] ? '<info>Yes</info>' : '<comment>No</comment>',
                ];
            })
        );

        return 0;
    }
}
