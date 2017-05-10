<?php

namespace Siggy;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'apikeys';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

	public $incrementing = false;

    protected $fillable = ['id', 'secret', 'group_id'];
	
	public function group()
	{
		return $this->belongsTo('Group','group_id');
	}
}