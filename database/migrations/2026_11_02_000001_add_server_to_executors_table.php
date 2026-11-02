<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the `server` column, so an executor limited to servers keeps one
 * record per server. The unique `name` and `server` pair also guards the
 * lock row that servers deploying at the same time share.
 */
return new class extends Migration
{
    public function up(): void
    {
        $table = config('pharaonic.executor.table', 'executors');
        $schema = Schema::connection(config('pharaonic.executor.connection', config('database.default')));

        if ($schema->hasColumn($table, 'server')) {
            return;
        }

        $schema->table($table, function (Blueprint $table) {
            $table->string('server')->nullable();
            $table->unique(['name', 'server']);
        });
    }

    public function down(): void
    {
        $table = config('pharaonic.executor.table', 'executors');
        $schema = Schema::connection(config('pharaonic.executor.connection', config('database.default')));

        if (! $schema->hasColumn($table, 'server')) {
            return;
        }

        // Laravel 10 drops SQLite columns through doctrine/dbal unless native
        // operations are enabled; later Laravel versions always use them.
        $native = method_exists(Builder::class, 'useNativeSchemaOperationsIfPossible');
        $previous = $native ? Builder::$alwaysUsesNativeSchemaOperationsIfPossible : null;

        if ($native) {
            Builder::useNativeSchemaOperationsIfPossible();
        }

        try {
            $schema->table($table, function (Blueprint $table) {
                $table->dropUnique(['name', 'server']);
            });

            $schema->table($table, function (Blueprint $table) {
                $table->dropColumn('server');
            });
        } finally {
            if ($native) {
                Builder::useNativeSchemaOperationsIfPossible($previous);
            }
        }
    }
};
