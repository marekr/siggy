<?php defined('SYSPATH') or die('No direct script access.');

/**
* User Model
*
* @package		SimpleAuth
* @author			thejw23
* @copyright		(c) 2010 thejw23
* @license		http://www.opensource.org/licenses/isc-license.txt
* @version		2.0
* @last change	
* 
* based on KohanaPHP Auth and Simple_Modeler
*/

class Model_Auth_Users extends authmodeler {

	protected $table_name = 'users';
	
	protected $unique = '';
	protected $second = '';
	protected $password = '';
		
	protected $data = array('id' => '',
						'username' => '',
						'password' => '',
						'email' => '',
						'logins' => '',
						'admin' => '',
						'active' => '',
						'active_to'=>'',
						'moderator' => '',
						'ip_address'=>'',
						'last_ip_address'=>'',
						'time_stamp'=>'',
						'last_time_stamp' => '',
						'time_stamp_created'=>''); 

	public $timestamp = array ();
	
	public function __construct($id = NULL)
	{
		parent::__construct($id);
		
		$auth_config =  Kohana::$config->load('simpleauth');
		
		$this->unique = $auth_config['unique'];
		$this->second =  $auth_config['unique_second'];
		$this->password =  $auth_config['password']; 
	}

	public function get_user($unique, $pass)
	{
		$data =  db::select('*')->from($this->table_name)->where($this->unique,'=',$unique)->and_where($this->password,'=',$pass)->execute();

		if (count($data) === 1 AND $data = $data->current())
		{
			$this->data_original = (array) $data;
			$this->data = $this->data_original; 
		}
	}
	
	public function getUserByEmail($email)
	{
		$data =  db::select('*')->from($this->table_name)->where('email','=',$email)->execute();

		if (count($data) === 1 AND $data = $data->current())
		{
			$this->data_original = (array) $data;
			$this->data = $this->data_original; 
		}
	}
	
	/**
	 * Check if username exists in database.
	 *
	 * @param string $name username to check
	 * @param string $second second username to check 	 
	 * @return boolean
	 */
	public function user_exists($name = NULL, $second = NULL)
	{
		if (!empty($second))
		{
			return count(db::select('id')->from($this->table_name)->where($this->unique, '=' , $name)->or_where($this->second, '=', $second)->execute());
		}
		else
		{
			return count(db::select('id')->from($this->table_name)->where($this->unique, '=' ,$name)->execute());
		}
	}
	
} // End Auth Users Model