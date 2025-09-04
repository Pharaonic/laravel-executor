<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;

class CreateExecutorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(config('pharaonic.executor.connection', config('database.default')))
            ->create(config('pharaonic.executor.table', 'executors'), function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('type')->default(ExecutorType::Always);
                $table->string('name');
                $table->json('tags')->nullable();
                $table->json('servers')->nullable();
                $table->integer('batch')->default(1);
                $table->integer('executed')->default(0);
                $table->timestamp('last_executed_at')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(config('pharaonic.executor.connection', config('database.default')))
            ->dropIfExists(config('pharaonic.executor.table', 'executors'));
    }
}
