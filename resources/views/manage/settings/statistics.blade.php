@extends('layouts.manage',[
							'title' => 'siggy.manage: chainmap settings'
						])

@section('content')
{!! Form::model($group, ['url' => 'manage/settings/statistics', 'method' => 'post']) !!}

	<fieldset>
		<legend>Statistics</legend>
		{!! Form::yesNo('stats_enabled', 'Record usage statistics?', 'If yes, siggy will record the number of sigs added, number of sigs edited and WHs mapped on a daily basis for each character. The recorded data is only currently avaliable as the in group stats board viewable by anyone who has access to your group.') !!}
		{!! Form::yesNo('record_jumps', 'Record pilot jump statistics?', 'If yes, siggy will gather per hour, the character jump totals similar to the eve API jumps for systems and display them together as a comparison statistic. Of course this setting depends on users having siggy open in order for the jumps to be recorded.') !!}
	</fieldset>
	
	<fieldset>
		<legend>Leaderboard Point Multipliers</legend>
		{!! Form::bsText('stats_sig_add_points', 'Signature Addition Point Multiplier', 'Point multiplier for each signature addition.  Valid values are from 0 to 1000. Decimal values permitted.') !!}
		{!! Form::bsText('stats_sig_update_points', 'Signature Update Point Multiplier', 'Point multiplier for each signature update.  Valid values are from 0 to 1000. Decimal values permitted.') !!}
		{!! Form::bsText('stats_wh_map_points', 'Wormholes Mapped Multiplier', 'Point multiplier for each wormhole mapped. Valid values are from 0 to 1000. Decimal values permitted.') !!}
		{!! Form::bsText('stats_pos_add_points', 'POS Addition Multiplier', 'Point multiplier for each POS addition. Valid values are from 0 to 1000. Decimal values permitted.') !!}
		{!! Form::bsText('stats_pos_update_points', 'POS Update Multiplier', 'Point multiplier for each POS update. Valid values are from 0 to 1000. Decimal values permitted.') !!}
	</fieldset>
	
	{!! Form::actionButtons() !!}

{!! Form::close() !!}
@endsection