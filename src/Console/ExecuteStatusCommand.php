<?php

namespace Pharaonic\Laravel\Executor\Console;

use Illuminate\Console\Command;
use Pharaonic\Laravel\Executor\Models\Executor;
use Pharaonic\Laravel\Executor\Services\ExecutorService;
use Pharaonic\Laravel\Executor\Services\ExecutorServices;

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
            ['Name', 'Type', 'Tag', 'Executed'],
            $executors->map(function ($executor) {
                return [
                    $executor['name'],
                    ucfirst($executor['type']->name),
                    $executor['tag'],
                    $executor['model']->executed > 0 ? '<info>Yes</info>' : 'No',
                ];
            })
        );

        return 0;
    }
}
