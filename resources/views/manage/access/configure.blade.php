
@extends('layouts.manage',[
							'title' => 'siggy.manage: Configure Access'
						])

@section('content')
<h2>Configure Access</h2>
<div class="pull-right">
	<a href="{{url('manage/access/add')}}" class='btn btn-primary'><i class="fa fa-plus-circle fa-fw"></i>&nbsp;Add Access</a>
</div>
<div class="clearfix"></div>
<br />
<table class='table table-striped'>
	<thead>
		<tr>
			<th>Username</th>
			<th width="12%">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	@foreach($users as $user)
		<tr>
			<td>{{$user->username}}</td>
			<td>
				<a href="{{url('manage/access/edit/'.$user->user_id)}}" class='btn btn-xs btn-default'><i class="fa fa-pencil"></i>&nbsp;Edit</a>
				<a href="{{url('manage/access/remove/'.$user->user_id)}}" class='btn btn-xs btn-danger'><i class="fa fa-trash"></i>&nbsp;Remove</a>
			</td>
		</tr>
	@endforeach
	</tbody>
</table>
@endsection