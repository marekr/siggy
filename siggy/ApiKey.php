<?php

namespace Siggy;

use Illuminate\Database\Eloquent\Model;
use Siggy\ApiKeyScope;

class ApiKey extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'apikeys';
    
	
	protected static $avaliableScopes = [
		];

	/**
	* The attributes that should be cast to native types.
	*
	* @var array
	*/
	protected $casts = [
		'scopes' => 'array'
	];

	/**
	* Indicates if the model should be timestamped.
	*
	* @var bool
	*/
	public $timestamps = true;

	/**
	* Indicates if the IDs are auto-incrementing.
	*
	* @var bool
	*/
	public $incrementing = false;

	/**
	* The guarded attributes on the model.
	*
	* @var array
	*/
	protected $guarded = [];
	
	public function group()
	{
		return $this->belongsTo('Group','group_id');
	}

	protected static function boot()
	{
		parent::boot();

		static::creating(function ($model)
		{
			$model->generateIdSecret();
		});
	}

	protected static function avaliableScopes() : array
	{
		if(empty(self::$avaliableScopes))
		{
			self::$avaliableScopes = [
				new ApiKeyScope('chainsmaps_read','Read chainmaps'),
				new ApiKeyScope('systems_read', 'Read systems'),
				new ApiKeyScope('group_read', 'Read group'),
			];
		}
		return self::$avaliableScopes;
	}

	protected function generateIdSecret()
	{
		$this->attributes['id'] = str_random(16);
		$this->attributes['secret'] = str_random(32);
	}

	/**
	* Determine if the token has a given scope.
	*
	* @param  string  $scope
	* @return bool
	*/
	public function can($scope) : bool
	{
		if($this->scopes == null)
		{
			return false;
		}

		return in_array('*', $this->scopes) ||
				array_key_exists($scope, array_flip($this->scopes));
	}

	/**
	* Determine if the token is missing a given scope.
	*
	* @param  string  $scope
	* @return bool
	*/
	public function cant($scope) : bool
	{
		return !$this->can($scope);
	}
}