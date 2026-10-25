<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Brings an executors table created by 1.x (`executor` and `tag` columns)
 * to the current shape (`name`, `tags` and `servers`), keeping every record.
 * Tables created by later releases are left untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        $connection = config('pharaonic.executor.connection', config('database.default'));
        $table = config('pharaonic.executor.table', 'executors');
        $schema = Schema::connection($connection);

        if (! $schema->hasTable($table) || ! $schema->hasColumn($table, 'executor')) {
            return;
        }

        $schema->table($table, function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->json('tags')->nullable();
            $table->json('servers')->nullable();
        });

        DB::connection($connection)->table($table)->orderBy('id')->each(function (object $record) use ($connection, $table) {
            DB::connection($connection)->table($table)->where('id', $record->id)->update([
                'name' => $record->executor,
                'tags' => $record->tag === null ? null : json_encode([$record->tag]),
            ]);
        });

        // Laravel 10 drops SQLite columns through doctrine/dbal unless native
        // operations are enabled; later Laravel versions always use them.
        $native = method_exists(Builder::class, 'useNativeSchemaOperationsIfPossible');
        $previous = $native ? Builder::$alwaysUsesNativeSchemaOperationsIfPossible : null;

        if ($native) {
            Builder::useNativeSchemaOperationsIfPossible();
        }

        try {
            $schema->table($table, function (Blueprint $table) {
                $table->dropColumn(['executor', 'tag']);
            });
        } finally {
            if ($native) {
                Builder::useNativeSchemaOperationsIfPossible($previous);
            }
        }
    }

    public function down(): void
    {
        // The 1.x schema is not restored: 1.x cannot read the records of this release.
    }
};
