<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use \Character;
use \GroupBlacklistCharacter;
use \Auth;
use \Group;

class BlacklistController extends BaseController
{
	public $actionAcl = [
		'getList' => ['can_manage_group_members'],
		'getAdd' => ['can_manage_group_members'],
		'postAdd' => ['can_manage_group_members'],
		'getRemove' => ['can_manage_group_members']
	];

	public function getList()
	{
		$chars = Auth::$user->group->blacklistCharacters();
		
		return view('manage.blacklist.list', [
												'chars' => $chars
											]);
	}

	public function getRemove($id)
	{
		$entry = GroupBlackListCharacter::findByGroup(Auth::$user->groupID, $id);

		if($entry != null)
		{
			$entry->delete();
			flash('Blacklisted character removed succesfully')->success();
		}

		return redirect('manage/blacklist/list');
	}

	public function getAdd()
	{
		return view('manage.blacklist.form', [
												'mode' => 'add'
											]);
	}
	
	public function postAdd()
	{
		$data = [
				'reason' => $_POST['reason'] ?? '', 
				'character_name' => $_POST['character_name']
				];

		$validator = Validator::make($data, [
			'character_name' => 'required',
		]);

		$charSearchResults = array();
		if(!$validator->fails())
		{
			$charSearchResults = Character::searchEVEAPI( $_POST['character_name'], true );
			if( $charSearchResults == null )
			{
				$validator->errors()->add('character_name', 'EVE character not found');
			}
			else
			{
				$charSearchResults = current($charSearchResults);
				
				$char = GroupBlacklistCharacter::findByGroupAndChar(Auth::$user->groupID, $charSearchResults->id);
					
				if( $char != null )
				{
					$validator->errors()->add('character_name', 'The character is already blacklisted');
				}
			}
		}
			
	
		if( count($validator->errors()) == 0 )
		{
			$save = [
						'reason' => $_POST['reason'],
						'character_id' => $charSearchResults->id,
						'group_id' => Auth::$user->groupID
			];

			GroupBlacklistCharacter::create($save);
			
			flash('Character added to blacklist succesfully')->success();
			return redirect('manage/blacklist/list');
		}
		
		return redirect('manage/blacklist/add')
					->withErrors($validator)
					->withInput();
	}
}