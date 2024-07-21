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
    protected $description = 'Rollback the lastest executors that has been inserted.';

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

        $this->info('Executors has been rollbacked successfully.');

        return 0;
    }
}
