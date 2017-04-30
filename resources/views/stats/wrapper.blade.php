@extends('layouts.siggy_stats')

@section('content')
<div class="container">
	<div class="row">
		<div class="btn-toolbar" role="toolbar">
			<div class="btn-group">
				<a class="btn btn-default<?php echo ( $stats_mode == Siggy\DatePager::MODEWEEKLY ? ' active' : '') ?>" href="{{url("stats/{$sub_page}?".http_build_query(['year' => date("Y"), 'week' => date("W")])) }}">Weekly</a>
				<a class="btn btn-default<?php echo ( $stats_mode == Siggy\DatePager::MODEMONTHLY  ? ' active' : '') ?>" href="{{url("stats/{$sub_page}?".http_build_query(['year' => date("Y"), 'month' => date("n")])) }}">Monthly</a>
				<a class="btn btn-default<?php echo ( $stats_mode == Siggy\DatePager::MODEYEARLY  ? ' active' : '') ?>" href="{{url("stats/{$sub_page}?".http_build_query(['year' => date("Y")])) }}">Yearly</a>
			</div>
		</div>
	</div>
	<br />
	<div class="row">
		<div class="btn-toolbar" role="toolbar">
			<div class="btn-group">
				<a class="btn btn-default<?php echo ( $sub_page == 'overview' ? ' active' : '') ?>" href="{{url("stats/?".http_build_query($current_date['urlargs']) )}}">Overview</a>
				<a class="btn btn-default<?php echo ( $sub_page == 'leaderboard' ? ' active' : '') ?>" href="{{url("stats/leaderboard?".http_build_query($current_date['urlargs']) )}}">Leaderboard</a>
				<a class="btn btn-default<?php echo ( $sub_page == 'pos_adds' ? ' active' : '') ?>" href="{{url("stats/pos_adds/?".http_build_query($current_date['urlargs']) )}}">POS Additions</a>
				<a class="btn btn-default<?php echo ( $sub_page == 'pos_updates' ? ' active' : '') ?>" href="{{url("stats/pos_updates/?".http_build_query($current_date['urlargs']) )}}">POS Updates</a>
				<a class="btn btn-default<?php echo ( $sub_page == 'sig_adds' ? ' active' : '') ?>" href="{{url("stats/sig_adds/?".http_build_query($current_date['urlargs']) )}}">Sig Additions</a>
				<a class="btn btn-default<?php echo ( $sub_page == 'sig_updates' ? ' active' : '') ?>" href="{{url("stats/sig_updates/?".http_build_query($current_date['urlargs']) )}}">Sig Updates</a>
				<a class="btn btn-default<?php echo ( $sub_page == 'wormholes' ? ' active' : '') ?>" href="{{url("stats/wormholes/?".http_build_query($current_date['urlargs']) )}}">Wormholes Mapped</a>
			</div>
		</div>
	</div>
</div>
<br />
<div class="container">
	<div class="row">
		<div class="well" style="text-align:center;font-size:16px;">
			<a style="float:left;" href="{{url("stats/{$sub_page}/?".http_build_query($previous_date['urlargs'])) }}">
				&lt;&lt; {{$previous_date['text']}}
			</a>
			<a  style="float:right;" href="{{url("stats/{$sub_page}/?".http_build_query($next_date['urlargs'])) }}">
				{{$next_date['text']}} &gt; &gt;
			</a>
			<strong>{{$current_date['text']}}</strong>
		</div>
	</div>
</div>
@yield('stats_content')
@endsection

