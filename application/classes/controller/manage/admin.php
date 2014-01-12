<?php

/**
 * User controller: user administration, also user accounts/profiles.
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */

class Controller_Manage_Admin extends Controller_App
{

   public $template = 'template/manage';

   public $auth_required = 'gadmin';

	public $secure_actions = array(
	// user actions
	'members' => array('login','gadmin')
	// the others are public (forgot, login, register, reset, noaccess)
	// logout is also public to avoid confusion (e.g. easier to specify and test post-logout page)
	);


   /**
    * View: Redirect admins to admin index, users to user profile.
    */
   public function action_index() 
   {
      if( Auth::$user->isAdmin() ) 
      {
         HTTP::redirect('manage/admin/groups');
      } 
      else 
      {
         HTTP::redirect('manage/admin/noaccess');
      }
   }

   /**
    * View: Access not allowed.
    */
   public function action_noaccess() 
   {
      $this->template->title = __('Access not allowed');
      $view = $this->template->content = View::factory('user/noaccess');
   }
   
	public function action_changeGroup()
	{
		if( !isset( $_POST['group'] ) )
		{
			HTTP::redirect('manage');
		}
		
		$group = intval($_POST['group']);
		
		if( !Auth::$user->isAdmin()  && !isset( Auth::$user->perms[ $group ] ) &&
			!( Auth::$user->perms[ $group ]['canManage'] == 1)
		) 
		{
			HTTP::redirect('manage/admin/noaccess');
		}
		else
		{
			Auth::$user->data['groupID'] = intval($_POST['group']);
			Auth::$user->save();
		}
      
	  HTTP::redirect('/manage');
      
	}

  
}