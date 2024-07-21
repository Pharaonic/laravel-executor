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
        Schema::create('executors', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type')->default(ExecutorType::Always);
            $table->string('executor');
            $table->integer('batch');
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
        Schema::dropIfExists('executors');
    }
}