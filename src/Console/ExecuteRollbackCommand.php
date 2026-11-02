<?php

namespace Pharaonic\Laravel\Executor\Console;

use Illuminate\Console\Command;
use Pharaonic\Laravel\Executor\Console\Concerns\RunsWithLock;
use Pharaonic\Laravel\Executor\Facades\Executor as ExecutorFacade;
use Pharaonic\Laravel\Executor\Models\Executor;

class ExecuteRollbackCommand extends Command
{
    use RunsWithLock;

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
        return $this->withLock(fn () => $this->rollback());
    }

    /**
     * Roll back the latest batches of this server.
     */
    private function rollback(): void
    {
        $steps = $this->option('steps');
        $records = ExecutorFacade::getRecords();
        $items = ExecutorFacade::getPool()->collect($records)->getItems();

        // The records of an executor limited to other servers stay for them.
        $records = $records->reject(fn (Executor $record) => isset($items[$record->name]) && ! $items[$record->name]->runsHere());

        $batches = $records->pluck('batch')
            ->filter(fn ($batch) => is_int($batch))
            ->unique()
            ->sortDesc()
            ->take(is_numeric($steps) ? (int) $steps : 1)
            ->all();

        if (empty($batches)) {
            $this->warn('There are no executors has been found.');

            return;
        }

        foreach ($items as $item) {
            if ($item->model && $item->runsHere() && in_array($item->model->batch, $batches)) {
                $this->info("Rolling back {$item->name}...");

                $item->rollback();
            }
        }

        Executor::query()->whereKey($records->whereIn('batch', $batches)->pluck('id')->all())->delete();

        $this->info('Executors has been rollback successfully.');
    }
}
