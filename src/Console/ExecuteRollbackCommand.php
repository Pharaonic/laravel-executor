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
    protected $signature = 'execute:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last inserted executors.';

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle()
    {
        $batch = Executor::max('batch');

        if ($batch === null) {
            $this->error('No executors found.');
            return 1;
        }

        Executor::where('batch', $batch)->delete();

        $this->info('Last inserted executors has been rolled back successfully.');

        return 0;
    }
}
