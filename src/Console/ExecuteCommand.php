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

        if ($name) {
            if (! isset($items[$name])) {
                $this->warn("There is no executor with name [$name].");
            }

            if ($items[$name]->isExecutable($tags)) {
                $toRun[] = $items[$name];
            }
        }

        if ($tags) {
            foreach ($items as $item) {
                if ($item->isExecutable($tags) && ! in_array($item, $toRun)) {
                    $toRun[] = $item;
                }
            }
        }

        if (empty($toRun)) {
            $this->warn('There are no executors need to be executed.');
        } else {
            $batch = $manager->getNextBatchNumber();

            foreach ($toRun as $item) {
                $this->info("Executing {$item->getName()}...");

                $item->run($batch);
            }
        }

        return 0;
    }
}
