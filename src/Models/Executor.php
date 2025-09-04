<?php

namespace Pharaonic\Laravel\Executor\Models;

use Illuminate\Database\Eloquent\Model;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;

/**
 * @property int $id
 * @property ExecutorType $type
 * @property string $name
 * @property array|null $tags
 * @property int|null $batch
 * @property int $executed
 * @property \Illuminate\Support\Carbon $last_executed_at
 * @method bool isNew()
 * @method bool isExecutable()
 * @method void execute()
 */
class Executor extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'name',
        'tags',
        'batch',
        'executed',
        'last_executed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'type' => ExecutorType::class,
        'tags' => 'array',
        'batch' => 'integer',
        'executed' => 'integer',
        'last_executed_at' => 'datetime',
    ];

    /**
     * Get the current connection name for the model.
     *
     * @return string|null
     */
    public function getConnectionName()
    {
        return config('pharaonic.executor.connection', parent::getConnectionName());
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('pharaonic.executor.table', parent::getTable());
    }

    /**
     * Check if the executor is new (never executed).
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->executed == 0;
    }

    /**
     * Check if the executor is executable.
     *
     * @return bool
     */
    public function isExecutable()
    {
        return $this->type->isAlways() || $this->isNew();
    }

    /**
     * Execute the executor action.
     *
     * @return void
     */
    public function execute()
    {
        $this->executed += 1;
        $this->last_executed_at = now();

        $this->save();
    }
}
