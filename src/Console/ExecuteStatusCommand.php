<?php

namespace Pharaonic\Laravel\Executor\Console;

use Illuminate\Console\Command;
use Pharaonic\Laravel\Executor\Services\ExecutorService;

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
    public function handle(ExecutorService $service)
    {
        $executors = $service->sync();

        $this->table(
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

        return 0;
    }
}
