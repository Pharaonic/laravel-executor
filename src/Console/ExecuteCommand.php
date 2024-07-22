<?php

namespace Pharaonic\Laravel\Executor\Console;

use Illuminate\Console\Command;
use Pharaonic\Laravel\Executor\Services\ExecutorService;

class ExecuteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute {name?}
                            {--tag= : Execute the executor with the specified tag}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the executors and execute them.';

    /**
     * Execute the console command.
     */
    public function handle(ExecutorService $service)
    {
        if (!$service->isExists()) {
            $this->warn('There are no executors need to be executed.');
        }

        $list = $service->sync();
        $name = $this->argument('name');
        $tag = $this->option('tag');

        $executors = $list
            ->filter(fn ($executor) => $executor['model']->executable)
            ->when($name, fn ($executors) => $executors->where('name', $name))
            ->when($tag, fn ($executors) => $executors->where('tag', $tag));

        if ($executors->isEmpty()) {
            $this->warn('There are no executors need to be executed.');
        }

        $executors->each(function ($executor) {
            $this->info("Executing {$executor['name']}...");

            (include $executor['path'])->handle();
            $executor['model']->execute();
        });

        return 0;
    }
}
