<?php

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
				'jump_log_enabled' => intval($_POST['jumpLogEnabled']),
				'jump_log_record_names' => intval($_POST['jumpLogRecordNames']),
				'jump_log_record_time' => intval($_POST['jumpLogRecordTime']),
				'jump_log_display_ship_type' => intval($_POST['jumpLogDisplayShipType']),
				'always_broadcast' => intval($_POST['alwaysBroadcast']),
				'chain_map_show_actives_ships' => intval($_POST['chain_map_show_actives_ships']),
				'allow_map_height_expand' => intval($_POST['allow_map_height_expand']),
				'chainmap_always_show_class' => intval($_POST['chainmap_always_show_class']),
				'chainmap_max_characters_shown' => intval($_POST['chainmap_max_characters_shown']),
			];

			$group->fill($save);
			$group->save();
			
			Message::add('success', ___('Chain map settings saved.'));
			
			HTTP::redirect('manage/settings/chain_map');
			return;
		}
		
		$resp = view('manage.settings.chain_map', [
												'errors' => $errors
											]);
		
		$this->response->body($resp);
	}
	
	public function action_statistics()
	{
		$group = Auth::$user->group;

		$errors = [];

		if ($this->request->method() == "POST") 
		{
			$save = [
				'stats_enabled' => intval($_POST['statsEnabled']),
				'record_jumps' => intval($_POST['recordJumps']),
				'stats_sig_add_points' => $this->___get_point_multiplier($_POST['stats_sig_add_points']),
				'stats_sig_update_points' => $this->___get_point_multiplier($_POST['stats_sig_update_points']),
				'stats_wh_map_points' => $this->___get_point_multiplier($_POST['stats_wh_map_points']),
				'stats_pos_add_points' => $this->___get_point_multiplier($_POST['stats_pos_add_points']),
				'stats_pos_update_points' => $this->___get_point_multiplier($_POST['stats_pos_update_points'])
			];
				
			$group->fill($save);
			$group->save();
				
			Message::add('success', ___('Chain map settings saved.'));
			
			HTTP::redirect('manage/settings/statistics');
			return;
		}

		$resp = view('manage.settings.statistics', [
												'errors' => $errors
											]);
		
		$this->response->body($resp);
	}
	
	private function ___get_point_multiplier($value)
	{
		//cast it as a float
		$value = (double)$value;
		
		if( $value > 1000 )
		{
			$value = 1000;
		}
		else if( $value < 0 )
		{
			$value = 0;
		}
		
		return (double)$value;
	}
   
	public function action_general()
	{
		$group = Auth::$user->group;

		$errors = [];
					
		if ($this->request->method() == "POST") 
		{
			$save = [
				'name' => $_POST['groupName'],
				'ticker' => $_POST['groupTicker'],
				'password_required' => intval($_POST['group_password_required']),
				'show_sig_size_col' => intval($_POST['showSigSizeCol']),
				'default_activity' => !empty($_POST['default_activity']) ? $_POST['default_activity'] : null
			];
			
			if( !empty($_POST['password']) && !empty($_POST['password_confirm']) )
			{
				if( $_POST['password'] == $_POST['password_confirm'] )
				{
					$save['password'] = sha1($_POST['password'].$group->password_salt);
				}
				else
				{
					Message::add( 'error', ___('Error: The password was not saved because it did not match between the two fields.') );
				}
			}
			$group->fill($save);
			$group->save();
				
			Message::add('success', ___('Settings saved.'));;
			HTTP::redirect('manage/settings/general');
			return;
		}

		$resp = view('manage.settings.general', [
												'errors' => $errors
											]);
		
		$this->response->body($resp);
	}
}