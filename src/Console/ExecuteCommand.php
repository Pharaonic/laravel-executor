<?php

namespace Pharaonic\Laravel\Executor\Console;

use Illuminate\Console\Command;

class ExecuteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute {name?}
                            {--tags= : Execute the executor with the specified tags}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the executors and execute them.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $manager = app('pharaonic.executor.manager');
        $items = $manager->pool->collect($manager->getRecords())->getItems();

        $name = $this->argument('name');
        $tags = $this->option('tags');
        $toRun = [];

        foreach ($items as $item) {
            if ($name && $item->name != $name) {
                continue;
            }

            if ($tags && ! array_intersect(explode(',', $tags), $item->tags)) {
                continue;
            }

            if ($item->isExecutable()) {
                $toRun[] = $item;
            }
        }
        
        if (empty($toRun)) {
            $this->warn('There are no executors need to be executed.');
        } else {
            $batch = $manager->getNextBatchNumber();

            foreach ($toRun as $item) {
                $this->info("Executing {$item->name}...");

                $item->run($batch);
            }
        }

        return 0;
    }
}
