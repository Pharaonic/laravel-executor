<?php

return [
    /**
     * The database connection that should be used by the executor.
     */
    'connection' => env('EXECUTOR_CONNECTION', config('database.default')),

    /**
     * The table that should be used to store the executors.
     */
    'table' => env('EXECUTOR_TABLE', 'executors'),

    /**
     * The lock that makes servers deploying at the same time run the
     * executors one after another. Use a cache store every server shares
     * (redis, database, memcached or dynamodb); null uses the default store.
     * `seconds` is how long a run may hold the lock and how long the next
     * server waits for it.
     */
    'lock' => [
        'store' => env('EXECUTOR_LOCK_STORE'),
        'seconds' => env('EXECUTOR_LOCK_SECONDS', 600),
    ],
];
