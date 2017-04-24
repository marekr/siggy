
@extends('layouts.manage',[
							'title' => 'siggy.manage: Configure Access'
						])

@section('content')
<h2>Configure Access</h2>
<div class="pull-right">
    <?php echo Html::anchor('manage/access/add', ___('<i class="fa fa-plus-circle fa-fw"></i>&nbsp;Add Access'), array('class' => 'btn btn-primary') ); ?>
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
			<td><?php echo $user->username; ?></td>
			<td><?php echo Html::anchor('manage/access/edit/'.$user->user_id, ___('<i class="fa fa-pencil"></i>&nbsp;Edit'), array('class' => 'btn btn-xs btn-default')); ?>
			<?php echo Html::anchor('manage/access/remove/'.$user->user_id, ___('<i class="fa fa-trash"></i>&nbsp;Remove'), array('class' => 'btn btn-xs btn-danger')); ?>
			</td>
		</tr>
	@endforeach
	</tbody>
</table>
@endsection