@extends('layouts.manage',[
							'title' => 'siggy.manage: chainmap settings'
						])

@section('content')
{!! Form::model($group, ['url' => 'manage/settings/chainmap', 'method' => 'post']) !!}

	<fieldset>
		<legend>Basic</legend>
		
		{!! Form::yesNo('always_broadcast', 'Always broadcast?', 'If yes, broadcasting cannot be disabled by users.') !!}

		{!! Form::yesNo('chain_map_show_actives_ships', 'Show ship types?', 'If yes, ship types will display next to the characters on hover over a system for the full activity list of characters.') !!}

		{!! Form::yesNo('allow_map_height_expand', 'Allow map height expansion?', 'If yes, you will be able to expand the map to twice its height with a resize') !!}

		{!! Form::yesNo('chainmap_always_show_class', 'Always show system class?','By default, when you rename wormhole systems, the class gets removed since you may name the hole c5a, c5b, etc. This instead keeps it always displayed.') !!}
		
		{!! Form::bsText('chainmap_max_characters_shown', 'Max characters shown') !!}

		{!! Form::yesNo('enable_wh_sig_link', 'Allow linking wormholes on map to signatures?','Linking wormholes on the map to signatures allows the deletion of wormholes when signatures are deleted and vice-versa. This however affects all maps.') !!}
		
	</fieldset>
	<fieldset>
		<legend>Jump Log</legend>	
		
		{!! Form::yesNo('jump_log_enabled', 'Enabled?', 'If yes, wormholes will have the jumps recorded. This is meant to display an approximate mass total and viewable in the chain map. ALL DATA regarding jumps is deleted immediately on wormhole deletion.') !!}

		{!! Form::yesNo('jump_log_record_names', 'Record pilot names?', 'If yes, pilot names of ships will be recorced and displayed, otherwise they will be blank.') !!}


		{!! Form::yesNo('jump_log_record_time', 'Record jump time?', 'If yes, the time of the jump will be recorded and displayed, otherwise it will be blank.') !!}


		{!! Form::yesNo('jump_log_display_ship_type', 'Display ship-type?', 'If yes, the ship type will be displayed. Otherwise it will be recorded for mass calculation purposes but hidden.') !!}


	</fieldset>
	{!! Form::actionButtons() !!}
{!! Form::close() !!}

@endsection