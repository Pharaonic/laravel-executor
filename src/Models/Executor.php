<?php

namespace Pharaonic\Laravel\Executor\Models;

use Illuminate\Database\Eloquent\Model;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;

class Executor extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
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
        'batch',
        'executed',
        'last_executed_at'
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
}
