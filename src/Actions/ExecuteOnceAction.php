<?php

namespace Pharaonic\Laravel\Executor\Actions;

use Pharaonic\Laravel\Executor\Models\Executor;

class ExecuteOnceAction
{
    public function handle($executorFilepath, $executorFile, $currentBatch)
    {
        if (!Executor::where('executor', pathinfo($executorFilepath, PATHINFO_FILENAME))->exists()) {
            try {
                $executorFile->handle();

                Executor::create([
                    'type' => $executorFile->getType(),
                    'executor' => pathinfo($executorFilepath, PATHINFO_FILENAME),
                    'batch' => $currentBatch,
                    'last_executed_at' => now(),
                ]);

                return true;
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
    }
}
