<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Facades\Auth;
use App\Facades\SiggySession;

class AccessController extends BaseController {

	public function getGroupPassword()
	{
		if(SiggySession::getGroup() == null)
		{
			//kick them off where hopefully the frontpagecontroller pushes them to the right spot
			return redirect('/');
		}

		$wrongPass = false;

		return view('access.group_password', [
												'group' => SiggySession::getGroup(),
												'settings' => $this->loadSettings(),
												'wrongPass' => $wrongPass
											]);
	}

	public function postGroupPassword(Request $requiest)
	{
		if( isset($_POST['group_password']) )
		{
			$pass = sha1($_POST['group_password'].SiggySession::getGroup()->password_salt);
			if( !empty(SiggySession::getGroup()->password) )
			{
				if( $pass === SiggySession::getGroup()->password )
				{
					Auth::user()->savePassword( SiggySession::getGroup()->id, $pass );
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
		if(SiggySession::getGroup() == null)
		{
			//kick them off where hopefully the frontpagecontroller pushes them to the right spot
			return redirect('/');
		}

		$reason = '';
		foreach(SiggySession::getGroup()->blacklistCharacters() as $char)
		{
			if($char->character_id == SiggySession::getCharacterId() )
			{
				$reason = $char->reason;
				break;
			}
		}

		
		return view('access.blacklisted', [
												'group' => SiggySession::getGroup(),
												'settings' => $this->loadSettings(),
												'reason' => $reason,
												'groupName' => SiggySession::getGroup()->name
											]);
	}
	
	public function getGroups()
	{
		$groups = SiggySession::accessibleGroups();

		return view('access.groups', [
												'group' => SiggySession::getGroup(),
												'settings' => $this->loadSettings(),
												'groups' => $groups
											]);
	}

	public function postGroups()
	{
		$groups = SiggySession::accessibleGroups();

		$selectedGroupId = intval($_POST['group_id']);
		if( $selectedGroupId && isset( $groups[ $selectedGroupId ] ) )
		{
			Auth::user()->groupID = $selectedGroupId;
			Auth::user()->save();
			SiggySession::reloadUserSession();

			return redirect('/');
		}
		
		return redirect()->back();
	}
}
