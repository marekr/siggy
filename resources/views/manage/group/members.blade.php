@extends('layouts.manage',[
							'title' => 'siggy.manage: chainmap settings'
						])

@section('content')
<h2>Group Members</h2>
<p>This page shows all corporations and characters that have access to your siggy group.</p>
<p>The EVEID listed on this page are the IDs that eve uses in game to reference your corp. You may find these from either your API info or sites such as http://evemaps.dotlan.net</p>
<div class="pull-right">
	<a href="{{url('manage/group/members/add')}}" class='btn btn-primary'><i class="fa fa-plus-circle fa-fw"></i>&nbsp;Add New Member</a>
</div>
<div class="clearfix"></div>

@if( count( $chainmaps ) > 0 )
	@each('manage.group.members_table', $table, 'table')
@else
	No chain maps found.
@endif

@endsection