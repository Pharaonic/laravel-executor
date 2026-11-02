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
     * The name of this server, matched against the executors' `$servers`
     * next to the server's IP addresses. Give each server a fixed name when
     * its addresses change, such as in containers.
     */
    'server' => env('EXECUTOR_SERVER'),

    /**
     * The lock that makes servers deploying at the same time run the
     * executors one after another. It is a row of the executors table, so
     * every server that shares the table shares the lock. `seconds` is how
     * long a run may hold the lock and how long the next server waits for it.
     */
    'lock' => [
        'seconds' => env('EXECUTOR_LOCK_SECONDS', 600),
    ],
];
