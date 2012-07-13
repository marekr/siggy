<?php defined('SYSPATH') or die('No direct script access.');

/**
* User Token Model
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

class Model_Auth_User_Tokens extends authmodeler {

	protected $table_name = 'user_tokens';

	protected $auto_trim = TRUE;

	protected $data = array('id' => '',
						'user_id' => '',
						'expires' => '',
						'time_stamp_created' => '',
						'user_agent' => '',
						'created' => '',
						'token' => '');
						
	protected $now;

	/**
	* Constructor
	*
	* @param integer $id unique token to be loaded	
	* @return void
	*/
	public function __construct($id = FALSE)
	{
				parent::__construct();

				$this->now = time();
		
				if ($id != NULL AND is_string($id))
				{
					$this->load($id,'token');

					if ($this->loaded())
					{
						if ( $this->data['expires'] < $this->now() ) 
						{
							$this->delete_expired();
							$this->clear_data();
							cookie::delete(Kohana::config('simpleauth.cookie_key'));
						}
					} 
					else
					{
						cookie::delete(Kohana::config('simpleauth.cookie_key'));
					}
				}
	}

	/**
	* Overload saving to set the created time and to create a new token
	* when the object is saved.	
	*
	* @return void
	*/
	public function save()
	{
		if ($this->data[$this->primary_key] == 0)
		{
			//$this->data['created'] = $this->now;
			$this->data['user_agent'] = sha1(Request::user_agent('browser'));
		}

		$this->data['token'] = $this->create_token();

		return parent::save();
	}

	/**
	 * Deletes all expired tokens.
	 *
	 * @return integer
	 */
	public function delete_expired()
	{
		return db::delete($this->table_name)->where('expires', '<=', $this->now)->execute();
	}
	
	
	/**
	 * Deletes all expired tokens and the user old tokens for current user_agent.
	 *
	 * @param integer $id unique user id to delete tokens
	 * @return mixed
	 */
	public function delete_user_tokens($id = 0, $all = FALSE)
	{
		$this->delete_expired();
		
		if (intval($id) === 0) 
			return FALSE;
		
		if ($all)
			return db::delete($this->table_name)->where('user_id', '=',$id)->execute();
		else
			return db::delete($this->table_name)->where('user_id', '=',$id)->where('user_agent','=',sha1(Request::user_agent('browser')))->execute();
	} 

	/**
	 * Finds a new unique token, using a loop to make sure that the token does
	 * not already exist in the database. This could potentially become an
	 * infinite loop, but the chances of that happening are very unlikely.
	 *
	 * @return  string
	 */
	protected function create_token()
	{
		while (TRUE)
		{
			// Create a random token
			$token = text::random('alnum', 32);

			// Make sure the token does not already exist
			if (count(db::select('id')->from($this->table_name)->where('token','=',$token)->execute()) === 0)
			{
				// A unique token has been found
				return $token;
			}
		}
	}

} // End User Tokens Model