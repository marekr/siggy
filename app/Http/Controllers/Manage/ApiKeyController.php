<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\Facades\Auth;
use Siggy\ApiKey;
use \Group;

class ApiKeyController extends BaseController
{
	public $actionAcl = [
		'getList' => ['can_manage_group_members'],
		'getAdd' => ['can_manage_group_members'],
		'postAdd' => ['can_manage_group_members'],
		'getRemove' => ['can_manage_group_members']
	];

	public function getList()
	{
		$keys = Auth::user()->group->apiKeys;
		return view('manage.apikeys.list', [
												'keys' => $keys
											]);
	}

	public function postRemove($id)
	{
		$entry = Auth::user()->group->apiKeys()->where('id',$id);

		if($entry != null)
		{
			$entry->delete();
			flash('Key removed succesfully')->success();
		}

		return redirect('manage/apikeys/list');
	}

	public function getAdd()
	{
		return view('manage.apikeys.form', [
												'mode' => 'add'
											]);
	}
	
	public function postAdd(Request $request)
	{
		$data = [
					'name' => $request->input('name')
				];

		$validator = Validator::make($data, [
			'name' => 'required',
		]);

		if($validator->passes())
		{
			$selectedScopes = $request->input('scopes', []);

			$data['scopes'] = [];
			foreach(ApiKey::avaliableScopes() as $scope)
			{
				if(array_key_exists($scope->id, $selectedScopes))
				{
					$data['scopes'][] = $scope->id;
				}
			}

			$data['group_id'] = Auth::user()->group->id;
			$key = ApiKey::create($data);
			return redirect('manage/apikeys/list');
		}
		
		return redirect('manage/apikeys/add')
					->withErrors($validator)
					->withInput();
	}
}