<?php

namespace Pharaonic\Laravel\Executor\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Pharaonic\Laravel\Executor\Actions\ExecuteAlwaysAction;
use Pharaonic\Laravel\Executor\Actions\ExecuteOnceAction;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;
use Pharaonic\Laravel\Executor\Services\ExecuteBatchServices;

class ExecuteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $executeBatchServices = new ExecuteBatchServices;
        $currentBatch = $executeBatchServices->getCurrentBatch();

        foreach (File::glob(base_path('executors/*')) as $executorFilepath) {
            $executorFile = include $executorFilepath;

            if ($executorFile->getType() == ExecutorType::Once) {
                (new ExecuteOnceAction)->handle($executorFilepath, $executorFile, $currentBatch);
            }

            if ($executorFile->getType() == ExecutorType::Always) {
                (new ExecuteAlwaysAction)->handle($executorFilepath, $executorFile, $currentBatch);
            }
        }
    }
}
