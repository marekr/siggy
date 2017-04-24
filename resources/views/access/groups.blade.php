@extends('layouts.siggy')

@section('content')
<div id="activity-siggy" class="wrapper" style="display:none">
	<div class="box">
		<div class="box-header">Select a group to access</div>
		<ul class='selection-list'>
		@if(count($groups))
			@foreach($groups as $group)
			<li onClick="javascript:$(this).find('form').submit();">
				<form action='{{URL::base(TRUE, TRUE)}}access/groups' method='POST'>
					<input type="hidden" name="_token" value="{{Auth::$session->csrf_token}}" />
					<input type='hidden' name='group_id' value='{{$group->id}}' />
					@if($group->password_required)
						<i class="fa fa-lock" aria-hidden="true"></i>
					@endif
					<div class="details">
						<b>{{$group->name}}</b>
					</div>
				</form>
			</li>
			@endforeach
		@else
			<li>
				<div class="details">
					No groups avaliable
				</div>
			</li>
		@endif
		</ul>
	</div>
</div>


<script type='text/javascript'>
$('#activity-siggy').show();
</script>
@endsection