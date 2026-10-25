<?php

namespace Pharaonic\Laravel\Executor\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Pharaonic\Laravel\Executor\Facades\Executor as ExecutorFacade;

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
     *
     * @return int
     */
    public function handle()
    {
        $seconds = config('pharaonic.executor.lock.seconds', 600);
        $seconds = is_numeric($seconds) ? (int) $seconds : 600;
        $store = config('pharaonic.executor.lock.store');
        $cache = Cache::store(is_string($store) ? $store : null)->getStore();

        if (! $cache instanceof LockProvider) {
            $this->runExecutors();

            return 0;
        }

        try {
            $cache->lock('pharaonic:executor:execute', $seconds)->block($seconds, fn () => $this->runExecutors());
        } catch (LockTimeoutException) {
            $this->error("Another server has been running the executors for more than {$seconds} seconds.");

            return 1;
        }

        return 0;
    }

    /**
     * Run the executors that should run.
     */
    private function runExecutors(): void
    {
        $items = ExecutorFacade::getPool()
            ->collect(ExecutorFacade::getRecords())
            ->getItems();

        $name = $this->argument('name');
        $tags = $this->option('tags');
        $tags = is_string($tags) && $tags !== '' ? explode(',', $tags) : [];
        $toRun = [];

        foreach ($items as $item) {
            if ($name && $item->name != $name) {
                continue;
            }

            if ($tags && ! array_filter($tags, fn ($tag) => in_array($tag, $item->executor->tags))) {
                continue;
            }

            if ($item->isExecutable()) {
                $toRun[] = $item;
            }
        }

        if (empty($toRun)) {
            $this->warn('There are no executors need to be executed.');
        } else {
            $batch = ExecutorFacade::getNextBatchNumber();

            foreach ($toRun as $item) {
                $this->info("Executing {$item->name}...");

                $item->run($batch);
            }
        }
    }
}
