<?php

namespace Pharaonic\Laravel\Executor\Console;

use Illuminate\Console\Command;
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
    protected $description = 'Rollback the lastest executors that has been inserted.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $batches = Executor::orderBy('batch', 'desc')->groupBy('batch')->limit($this->option('steps'))->pluck('batch')->toArray();

        if (empty($batches)) {
            $this->error('There are no executors has been found.');

            return 1;
        }

        $manager = app('pharaonic.executor.manager');
        $items = $manager->pool->collect($manager->getRecords())->getItems();

        foreach ($items as $item) {
            if ($item->model && in_array($item->model->batch, $batches)) {
                $this->info("Rolling back {$item->name}...");

                $item->rollback();
            }
        }

        Executor::whereIn('batch', $batches)->delete();

        $this->info('Executors has been rollback successfully.');

        return 0;
    }
}
