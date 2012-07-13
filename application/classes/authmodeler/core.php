<?php defined('SYSPATH') or die('No direct script access.');

/**
* Auth_Modeler  
*
* @package		SimpleAuth
* @author			thejw23
* @copyright		(c) 2010 thejw23
* @license		http://www.opensource.org/licenses/isc-license.txt
* @version		2.0
* @last change
*  
* modified version of Simple_Modeler, removed all methods not needed for Auth  
* class name changed to prevent conflicts while using original Simple_Modeler 
*/

class authmodeler_Core extends Model_Database {

	// database table name
	protected $table_name = '';
	
	// primary key for the table
	protected $primary_key = 'id';
	 		
	// store single record database fields and values
	protected $data = Array();
	protected $data_original = Array();
		
	// timestamp fields, they will be auto updated on db update
	// update is only if table has a column with given name
	//public $timestamp = Array('time_stamp');
	
	// timestamp fields updated only on db insert
	//public $timestamp_created = Array('time_stamp_created');
	
	// fetch only those fields, if empty select all fields
	public $select = '*';
	
	
	/**
	* Constructor
	*
	* @param integer|array $id unique record to be loaded	
	* @return void
	*/
	public function __construct($id = NULL)
	{
		parent::__construct();
          
		$this->load_columns();
		
		if ($id != NULL)
		{
			$this->load($id);
		}
	}
	
	/**
	* Return a static instance of Simple_Modeler.
	* Useful for one line method chaining.	
	*
	* @param string $model name of the model class to be created
	* @param integer|array $id unique record to be loaded	
	* @return object
	*/
	public static function factory($model, $id = FALSE)
	{
		$model = empty($model) ? __CLASS__ : 'Model_'.ucwords($model);
		return new $model($id);
	}
	
	/**
	* Create an instance of Simple_Modeler.
	* Useful for one line method chaining.	
	*
	* @param string $model name of the model class to be created
	* @param integer|array $id unique record to be loaded	
	* @return object
	*/
	public static function instance($model, $id = FALSE)
	{
		static $instance;
		$model = empty($model) ? __CLASS__ : 'Model_'.ucfirst($model);

		empty($instance) and $instance = new $model($id);
				
		if ( ! $instance instanceof $model)
			return  new $model($id);
			 
		return $instance;
	}

	/**
	*  Allows for setting data fields in bulk	
	*
	* @param array $data data passed to $data
	* @return object
	*/
	public function set_fields($data)
	{
		foreach ($data as $key => $value)
		{
			if (array_key_exists($key, $this->data))
			{
				$this->data[$key] = $value;
			}
		}
		
		return $this;
	}

	/**
	*  Saves the current $data to DB	
	*
	* @return mixed
	*/
	public function save()
	{
		$data_to_save = array_diff_assoc($this->data, $this->data_original);

		if (empty($data_to_save))
			return NULL;

		//$data_to_save = $this->check_timestamp($data_to_save, $this->loaded());

		// Do an update
		if ($this->loaded())
		{ 
				$result = count(db::update($this->table_name)->set($data_to_save)->where($this->primary_key, '=', $this->data[$this->primary_key])->execute());
				if ($result)
				{
					$this->data_original = $this->data;
					return $result;	
				}
		}
		else // Do an insert
		{
			$id = db::insert($this->table_name, array_keys($data_to_save))->values($data_to_save)->execute();
			if ($id)
			{
				$this->data[$this->primary_key] = $id;
				$this->data_original = $this->data;
			}
			else
				return FALSE;
			
			if ($id AND !empty($this->hash_field))
			{
				db::update($this->table_name)->set(array($this->hash_field => sha1($this->table_name.$id.$this->hash_suffix)))->where($this->primary_key, '=', $this->data[$this->primary_key])->execute();
			}
			
			return ($id);
		}
		return NULL;
	}
	
	/**
	* load single record based on unique field value	
	*
	* @param array|integer $value column value
	* @param string $key column name  	 
	* @return object
	*/
	public function load($value, $key = NULL)
	{
		(empty($key)) ? $key = $this->primary_key : NULL;
		
		$data = db::select($this->select)->from($this->table_name)->where($key, '=', $value)->execute();

		if (count($data) === 1 AND $data = $data->current())
		{
			$this->data_original = (array) $data;
			$this->data = $this->data_original; 
		}
	
		return $this;

	}
	
	/**
	* Deletes from db current record or condition based records 	
	*
	* @param array $what data to be deleted
	* @return mixed
	*/  
	public function delete()
	{
		if (intval($this->data[$this->primary_key]) !== 0) 
			return db::delete($this->table_name)->where($this->primary_key, '=', $this->data[$this->primary_key])->execute();

		return NULL;
	}
	
	
	/**
	*  clear values of $data and $data_original
	*
	* @return 
	*/
	public function clear_data()
	{
		array_fill_keys($this->data, '');
		array_fill_keys($this->data_original, '');
	}
	
	/**
	*  Set columns for select
	*
	* @param array $fields query select
	* @return object
	*/
	public function select($fields = array())
	{
		if (empty($fields)) 
			return $this;

		if (is_array($fields))
		{
			$this->select = $fields;
		}
		elseif(func_num_args() > 0)
		{
			$this->select = func_get_args();
		}

		return $this;
	} 

	/**
	*  check if data has been retrived from db and has a primary key value other than 0	
	*
	* @param string $field data key to be checked
	* @return boolean
	*/	
	public function loaded($field = NULL) 
	{ 
		(empty($field)) ? $field = $this->primary_key : NULL;
		return (intval($this->data[$field]) !== 0) ? TRUE : FALSE;
	}

	/**
	*  load table fields into $data	
	*
	* @return void
	*/
	public function load_columns() 
	{
		if ( ! empty($this->data) AND (empty($this->data_original)) )
		{
			$this->data_original =  $this->data;
			array_fill_keys($this->data, '');
			array_fill_keys($this->data_original, '');
		}
	}
	
	/**
	*  return current loaded data	
	*
	* @return array
	*/ 
	public function as_array()
	{
		return $this->data;
	}
	
	/**
	*  Checks if given key is a timestamp and should be updated	
	*
	* @param string $key key to be checked
	* @return array
	*/
	 public function check_timestamp($data, $loaded = FALSE)
	 {
		// update timestamp fields with current datetime
/*		if ($loaded)
		{
			if ( ! empty($this->timestamp) AND is_array($this->timestamp))
				foreach ($this->timestamp as $field)
					if (array_key_exists($field, $this->data_original))
					{
						$data[$field] = date('Y-m-d H:i:s');
					}
		}
		else
		{
			if ( ! empty($this->timestamp_created) AND is_array($this->timestamp_created))
				foreach ($this->timestamp_created as $field)
					if (array_key_exists($field, $this->data_original))
					{
						$data[$field] = date('Y-m-d H:i:s');
					}
		}
		
		return $data;
	*/
	 }
	 
	/**
	*  Magic get from $data	
	*
	* @param string $key key to be retrived
	* @return mixed
	*/
	public function __get($key)
	{
		if (array_key_exists($key, $this->data))
			return $this->data[$key];

		return NULL;
	}

	/**
	*  magic set to $data	
	*
	* @param string $key key to be modified
	* @param string $value value to be set
	* @return object
	*/
	public function __set($key, $value)
	{
		if (array_key_exists($key, $this->data) AND (empty($this->data[$key]) OR $this->data[$key] !== $value))
			return $this->data[$key] = $value;

		return NULL;
	}


	/**
	*  serialize only needed values (without DB connection)	
	*
	* @return array
	*/
	public function __sleep()
	{
		// Store only information about the object without db property
		return array_diff(array_keys(get_object_vars($this)), array('db'));
	}
	
	/**
	*  unserialize	
	*
	* @return void
	*/
	public function __wakeup()
	{
		// Initialize database
		$this->db = Database::instance();
	}
	
	public function __isset($key)
	{
		return isset($this->data[$key]);
	}

}