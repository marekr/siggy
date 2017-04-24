@extends('layouts.manage',[
							'title' => 'siggy.manage: add member'
						])

@section('content')
<?php
$select = array();
foreach($chainmaps as $c )
{
	$select[$c->chainmap_id] = $c->chainmap_name;
}

$type = array('corp' => 'Corp', 'char' => 'Character');
?>


<form class="form-horizontal" action="<?php echo URL::base(TRUE,TRUE).'manage/group/addMember/2'; ?>" method="post">
	<h3>Add Group Member</h3>
	<input type="hidden" name="act" value="doAdd" />
	<input type="hidden" name="eveID" value="{{$eveID}}" />
	<input type="hidden" name="accessName" value="{{$accessName}}" />
	<input type="hidden" name="memberType" value="{{$memberType}}" />

	@if( $memberType == 'corp' )
	<p>
		<img src="https://image.eveonline.com/Corporation/{{$eveID}}_64.png" width="64" height="64" />&nbsp;&nbsp;<strong>{{$accessName}}</strong>
	</p>
	@else
	<p>
		<img src="https://image.eveonline.com/Character/{{$eveID}}_64.jpg" width="64" height="64" />&nbsp;&nbsp;<strong>{{$accessName}}</strong>
	</p>
	@endif

	@if( count($chainmaps) > 0 )
		{!! Form::bsSelect('chainmap_id', 'Chainmaps', $select, '', 0) !!}
	@endif
	<br />
	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Add member</button>
		<button type="button" class="btn" onclick="history.go(-2);return false;">Cancel</button>
	</div>
</form>
@endsection