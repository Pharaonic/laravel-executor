<?php

namespace Pharaonic\Laravel\Executor\Actions;

use Pharaonic\Laravel\Executor\Models\Executor;

class ExecuteAlwaysAction
{
    public function handle($executorFilepath, $executorFile, $currentBatch)
    {
        $fileName = pathinfo($executorFilepath, PATHINFO_FILENAME);

        try {
            $executorFile->handle();

            if ($executor = Executor::where('type', $executorFile->getType())->where('executor', $fileName)->first()) {
                $executor->increment('executed');
                return true;
            }

            Executor::create([
                'type' => $executorFile->getType(),
                'executor' => pathinfo($executorFilepath, PATHINFO_FILENAME),
                'batch' => $currentBatch,
                'last_executed_at' => now()
            ]);

            return true;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
