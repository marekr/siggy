<?php

use Siggy\View;

class Controller_Manage_Settings extends Controller_Manage
{
	/*
	 * @var string Filename of the template file.
	 */
	public $template = 'template/manage';

	/*
	 * Controls access for the whole controller, if not set to FALSE we will only allow user roles specified.
	 */
	public $auth_required = 'gadmin';

	public $secure_actions = array(
		'general' => array('can_manage_settings'),
		'chain_map' => array('can_manage_settings'),
		'statistics' => array('can_manage_settings')
	);

	public function action_index() 
	{
		if( Auth::$user->isGroupAdmin() || Auth::$user->admin ) 
		{
			HTTP::redirect('manage/logs/activity');
		} 
		else 
		{
			HTTP::redirect('manage/access/denied');
		}
	}
	
	public function action_chain_map()
	{
		$group = Auth::$user->group;

		$errors = [];
					
		if ($this->request->method() == "POST") 
		{
			$save = [
				'jump_log_enabled' => intval($_POST['jump_log_enabled']  ?? 0),
				'jump_log_record_names' => intval($_POST['jump_log_record_names'] ?? 0),
				'jump_log_record_time' => intval($_POST['jump_log_record_time'] ?? 0),
				'jump_log_display_ship_type' => intval($_POST['jump_log_display_ship_type'] ?? 0),
				'always_broadcast' => intval($_POST['always_broadcast'] ?? 0),
				'chain_map_show_actives_ships' => intval($_POST['chain_map_show_actives_ships'] ?? 0),
				'allow_map_height_expand' => intval($_POST['allow_map_height_expand'] ?? 0),
				'chainmap_always_show_class' => intval($_POST['chainmap_always_show_class'] ?? 0),
				'chainmap_max_characters_shown' => intval($_POST['chainmap_max_characters_shown'] ?? 10),
			];

			$validator = Validator::make($save, [
				'chainmap_max_characters_shown' => 'required|integer|max:100|min:0',
			]);


			if(!$validator->fails())
			{
				$group->fill($save);
				$group->save();
				
				Message::add('success', ___('Chain map settings saved.'));
				
				HTTP::redirect('manage/settings/chain_map');
				return;
			}
			else
			{
				View::share('errors', $validator->errors());
			}
		}
		
		$resp = view('manage.settings.chain_map');
		
		$this->response->body($resp);
	}
	
	public function action_statistics()
	{
		$group = Auth::$user->group;

		if ($this->request->method() == "POST") 
		{
			$save = [
				'stats_enabled' => intval($_POST['stats_enabled'] ?? 0),
				'record_jumps' => intval($_POST['record_jumps'] ?? 0),
				'stats_sig_add_points' => $_POST['stats_sig_add_points'],
				'stats_sig_update_points' => $_POST['stats_sig_update_points'],
				'stats_wh_map_points' => $_POST['stats_wh_map_points'],
				'stats_pos_add_points' => $_POST['stats_pos_add_points'],
				'stats_pos_update_points' => $_POST['stats_pos_update_points']
			];

			$validator = Validator::make($save, [
				'stats_enabled' => 'required|boolean',
				'record_jumps' => 'required|boolean',
				'stats_sig_add_points' => 'required|integer|max:1000|min:0',
				'stats_sig_update_points' => 'required|integer|max:1000|min:0',
				'stats_wh_map_points' => 'required|integer|max:1000|min:0',
				'stats_pos_add_points' => 'required|integer|max:1000|min:0',
				'stats_pos_update_points' => 'required|integer|max:1000|min:0',
			]);

			if(!$validator->fails())
			{
				$group->fill($save);
				$group->save();
					
				Message::add('success', ___('Chain map settings saved.'));
				
				HTTP::redirect('manage/settings/statistics');
				return;
			}
			else
			{
				View::share('errors', $validator->errors());
			}
		}

		$resp = view('manage.settings.statistics');
		
		$this->response->body($resp);
	}
   
	public function action_general()
	{
		$group = Auth::$user->group;
					
		if ($this->request->method() == "POST") 
		{
			$save = [
				'name' => $_POST['name'],
				'ticker' => $_POST['ticker'],
				'password_required' => intval($_POST['password_required'] ?? 0),
				'show_sig_size_col' => intval($_POST['show_sig_size_col'] ?? 0),
				'default_activity' => $_POST['default_activity'] ?? null,
				'password' => $_POST['password'],
				'password_confirmation' => $_POST['password_confirmation']
			];

			$validator = Validator::make($save, [
				'name' => 'required|alpha_dash|min:3',
				'ticker' => 'required|min:3',
				'password_required' => 'required|boolean',
				'show_sig_size_col' => 'required|boolean',
				'default_activity' => 'nullable|string',
				'password' => 'nullable|confirmed',
				'password_confirmation' => 'required_with:password'
			]);


			if(!$validator->fails())
			{
				if( !empty($save['password']) &&
					!empty($save['password_confirmation']) )
				{
					$save['password'] = sha1($save['password'].$group->password_salt);
				}
				else
				{
					unset($save['password']);
				}
				
				$group->fill($save);
				$group->save();
					
				Message::add('success', ___('Settings saved.'));;
				HTTP::redirect('manage/settings/general');
				return;
			}
			else
			{
				View::share('errors', $validator->errors());
			}
		}

		$resp = view('manage.settings.general');
		
		$this->response->body($resp);
	}
}