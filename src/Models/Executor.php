<?php

namespace Pharaonic\Laravel\Executor\Models;

use Illuminate\Database\Eloquent\Model;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;

/**
 * @property int $id
 * @property ExecutorType $type
 * @property string $name
 * @property array<array-key, mixed>|null $tags
 * @property array<array-key, mixed>|null $servers
 * @property int|null $batch
 * @property int $executed
 * @property \Carbon\CarbonInterface|null $last_executed_at
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
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'name',
        'tags',
        'servers',
        'batch',
        'executed',
        'last_executed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => ExecutorType::class,
        'tags' => 'array',
        'servers' => 'array',
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
        $connection = config('pharaonic.executor.connection', parent::getConnectionName());

        return is_string($connection) ? $connection : null;
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        $table = config('pharaonic.executor.table', parent::getTable());

        return is_string($table) ? $table : parent::getTable();
    }

    /**
     * Check if the executor is new (never executed).
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->executed === 0;
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
