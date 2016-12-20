<?php

use Carbon\Carbon;
use Pheal\Pheal;

class GroupMember {
	const TypeCorp = 'corp';
	const TypeChar = 'char';

	public $id;
	public $eveID;
	public $memberType;
	public $groupID;
	public $accessName;

	public $corporation = null;
	public $character = null;
	public $group = null;

	public function __construct($props)
	{
		foreach ($props as $key => $value) 
		{
    		$this->$key = $value;
		}
	}
	
	public function save($props)
	{
		foreach ($props as $key => $value) 
		{
    		$this->$key = $value;
		}

		DB::update('groupmembers')
			->set( $props )
			->where('id', '=',  $this->id)
			->execute();
	}

	public static function create($props)
	{
		DB::insert('groupmembers', array_keys($props) )
				->values(array_values($props))
				->execute();

		return new GroupMember($props);
	}

	public function corporation()
	{
		if( $this->corporation == null || $this->corporation->id != $this->eveID )
		{
			$this->corporation = Corporation::find($this->eveID);
		}

		return $this->corporation;
	}
	
	public function group()
	{
		if( $this->group == null || $this->group->id != $this->groupID )
		{
			$this->group = Group::find($this->groupID);
		}

		return $this->group;
	}

	public static function find(int $id)
	{
		$data = DB::query(Database::SELECT, 'SELECT * FROM groupmembers WHERE id=:id')
												->param(':id', $id)
												->execute()
												->current();

		if($data != null)
		{
			return new GroupMember($data);
		}

		return null;
	}

	public static function findByType(string $type, int $id)
	{
		$data = DB::query(Database::SELECT, 'SELECT * FROM groupmembers WHERE eveID=:id AND memberType=:type')
												->param(':id', $id)
												->param(':type', $type)
												->execute()
												->as_array();

		$results = [];
		if($data != null)
		{
			foreach($data as $item)
			{
				$results[] = new GroupMember($item);
			}
		}

		return $results;
	}

	public static function findByGroupAndType(int $groupId, string $type, int $id)
	{
		$data = DB::query(Database::SELECT, 'SELECT * FROM groupmembers 
													WHERE eveID=:id AND 
														memberType=:type AND 
														groupID = :groupId')
												->param(':id', $id)
												->param(':type', $type)
												->param(':groupId', $groupId)
												->execute()
												->current();

		if($data != null)
		{
			return new GroupMember($data);
		}

		return null;
	}

	public static function findByGroup(int $groupId)
	{
		$data = DB::query(Database::SELECT, 'SELECT * FROM groupmembers WHERE groupID=:id')
												->param(':id', $groupId)
												->execute()
												->as_array();

		$results = [];
		if($data != null)
		{
			foreach($data as $item)
			{
				$results[] = new GroupMember($item);
			}
		}

		return $results;
	}
}