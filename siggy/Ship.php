<?php

namespace Siggy;

use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ships';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
	public $incrementing = false;

    protected $fillable = ['id', 'name', 'class', 'mass'];
}