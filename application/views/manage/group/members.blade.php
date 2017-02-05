@extends('layouts.manage',[
							'title' => 'siggy.manage: chainmap settings'
						])

@section('content')
<h2><?php echo ___('Group Members') ?></h2>
<p>This page shows all corporations and characters that have access to your siggy group.</p>
<p>The EVEID listed on this page are the IDs that eve uses in game to reference your corp. You may find these from either your API info or sites such as http://evemaps.dotlan.net</p>
<div class="pull-right">
	<?php echo Html::anchor('manage/group/addMember', ___('<i class="fa fa-plus-circle fa-fw"></i>&nbsp;Add New Member'), array('class' => 'btn btn-primary') ); ?>
</div>
<div class="clearfix"></div>

<?php if( count( $chainmaps ) > 0 ): ?>
	<?php foreach( $chainmaps as $c ): ?>
		<h2 class="tableHeader"><?php echo $c['chainmap_name'] ?></h2>
		<?php echo $membersHTML[ $c['chainmap_id'] ]; ?>
	<?php endforeach; ?>
<?php else: ?>
	No chain maps found.
<?php endif; ?>
@endsection