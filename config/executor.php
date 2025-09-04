<?php

return [
    /**
     * The database connection that should be used by the executor.
     */
    'connection' => env('DB_CONNECTION', config('database.default')),

    /**
     * The table that should be used to store the executors.
     */
    'table'      => 'executors',
];