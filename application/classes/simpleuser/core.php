<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
* Simple_User - simple class to store user data in session
*
* @package		SimpleAuth
* @author			thejw23
* @copyright		(c) 2010 thejw23
* @license		http://www.opensource.org/licenses/isc-license.txt
* @version		2.0
* @last change	
*/

class simpleuser_Core {

	// user data
	protected $data = Array();
	
	// is loaded
	protected $loaded = FALSE;
	
	/**
	*  return user data
	*
	* @return array
	*/ 
	public function as_array()
	{
		return $this->data;
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
		{
			return $this->data[$key];
		}
		return NULL;
	}

	/**
	*  set user data	
	*
	* @param array $data array with user data
	* @return void
	*/
	public function set_user($data = NULL)
	{
		if (!empty($data) AND is_array($data))
		{
			$this->data = $data;
			$this->loaded = TRUE;
		}
	}
	
	/**
	*  clear user data	
	*
	* @return void
	*/
	public function unset_user()
	{
			$this->data = array();
			$this->loaded = FALSE;
	}

}
?>