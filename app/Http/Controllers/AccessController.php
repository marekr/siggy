<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \Auth;

class AccessController extends BaseController {

	public function getGroupPassword()
	{
		if(Auth::$session->group == null)
		{
			//kick them off where hopefully the frontpagecontroller pushes them to the right spot
			return redirect('/');
		}

		$wrongPass = false;

		
		return view('access.group_password', [
												'group' => Auth::$session->group,
												'settings' => $this->loadSettings(),
												'wrongPass' => $wrongPass
											]);
	}

	public function postGroupPassword(Request $requiest)
	{
		if( isset($_POST['group_password']) )
		{
			$pass = sha1($_POST['group_password'].Auth::$session->group->password_salt);
			if( !empty(Auth::$session->group->password) )
			{
				if( $pass === Auth::$session->group->password )
				{
					Auth::$user->savePassword( Auth::$session->group->id, $pass );
					return redirect('/');
				}
				else
				{
					$wrongPass = true;
				}
			}
		}
		
		return redirect()->back()
                    ->withInput();
	}

	public function getBlacklisted()
	{
		if(Auth::$session->group == null)
		{
			//kick them off where hopefully the frontpagecontroller pushes them to the right spot
			return redirect('/');
		}

		$reason = '';
		foreach(Auth::$session->group->blacklistCharacters() as $char)
		{
			if($char->character_id == Auth::$session->character_id )
			{
				$reason = $char->reason;
				break;
			}
		}

		
		return view('access.blacklisted', [
												'group' => Auth::$session->group,
												'settings' => $this->loadSettings(),
												'reason' => $reason,
												'groupName' => Auth::$session->group->name
											]);
	}
	
	public function getGroups()
	{
		$groups = Auth::$session->accessibleGroups();

		return view('access.groups', [
												'group' => Auth::$session->group,
												'settings' => $this->loadSettings(),
												'groups' => $groups
											]);
	}

	public function postGroups()
	{
		$groups = Auth::$session->accessibleGroups();

		$selectedGroupId = intval($_POST['group_id']);
		if( $selectedGroupId && isset( $groups[ $selectedGroupId ] ) )
		{
			Auth::$user->groupID = $selectedGroupId;
			Auth::$user->save();
			Auth::$session->reloadUserSession();

			return redirect('/');
		}
		
		return redirect()->back();
	}
}
