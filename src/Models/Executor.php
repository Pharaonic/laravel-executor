<?php

namespace Pharaonic\Laravel\Executor\Models;

use Illuminate\Database\Eloquent\Model;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;

/**
 * @property int $id
 * @property ExecutorType $type
 * @property string $executor
 * @property string $tag
 * @property int $batch
 * @property int $executed
 * @property \Carbon\Carbon $last_executed_at
 * @property-read string $executable
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
        'executor',
        'tag',
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
        'batch' => 'integer',
        'executed' => 'integer',
        'last_executed_at' => 'datetime',
    ];

    /**
     * Get the executable attribute.
     *
     * @return void
     */
    public function getExecutableAttribute()
    {
        return $this->type->isAlways() || ($this->type->isOnce() && $this->executed == 0);
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
