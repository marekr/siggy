@extends('layouts.manage',[
							'title' => 'siggy.manage: remove user from chainmap'
						])

@section('content')
<h2>Confirm deletion</h2>
<form action="{{url('manage/chainmaps/access/remove/'.$id)}}" method="POST">
	<input type="hidden" value="1" name="confirm" />
	<p>
		Are you sure you want to remove '<?php echo $member->accessName; ?>' from the group access?
	</p>
	<button type="submit" class="btn btn-danger">Confirm Deletion</button>
	<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
</form>
@endsection