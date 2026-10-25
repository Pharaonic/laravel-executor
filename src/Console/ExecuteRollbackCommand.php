<?php

namespace Pharaonic\Laravel\Executor\Console;

use Illuminate\Console\Command;
use Pharaonic\Laravel\Executor\Facades\Executor as ExecutorFacade;
use Pharaonic\Laravel\Executor\Models\Executor;

class ExecuteRollbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:rollback
                            {--steps=1 : The number of executors to rollback}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the latest executors that have been inserted.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $steps = $this->option('steps');

        $batches = Executor::query()
            ->orderBy('batch', 'desc')
            ->groupBy('batch')
            ->limit(is_numeric($steps) ? (int) $steps : 1)
            ->pluck('batch')
            ->toArray();

        if (empty($batches)) {
            $this->warn('There are no executors has been found.');

            return 0;
        }

        $items = ExecutorFacade::getPool()
            ->collect(ExecutorFacade::getRecords())
            ->getItems();

        foreach ($items as $item) {
            if ($item->model && in_array($item->model->batch, $batches)) {
                $this->info("Rolling back {$item->name}...");

                $item->rollback();
            }
        }

        Executor::query()->whereIn('batch', $batches)->delete();

        $this->info('Executors has been rollback successfully.');

        return 0;
    }
}
