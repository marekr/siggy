<?php 
	$subgroups = $group->subgroups->find_all();
?>
<h1><?php echo __('Chainmaps List') ?></h1>
<div class="info">
This page lists all the chainmaps that are setup. There is a "default" chain map which cannot ever be deleted but can have its settings modified. All other chainmaps can be freely modified and removed.
</div>

<?php echo Html::anchor('manage/chainmaps/add', __('<i class="fa fa-plus-circle fa-fw"></i>&nbsp;Add New Sub Group'), array('class' => 'btn btn-primary pull-right') ); ?>
<div class="clearfix"></div>
<br />			
<?php if( count( $subgroups->as_array() ) > 0 ): ?>
<table class="table table-striped" width="100%">
	<thead>
			<tr>
				<th width="60%">Subgroup</th>
				<th width="15%"># Members</th>
				<th width="25%">Options</th>
			</tr>
		</thead>
	<tbody>
		<?php foreach( $subgroups as $s ): ?>
		<tr>
			<td><?php echo $s->sgName ?></td>
			<td><?php echo $s->groupmembers->count_all(); ?></td>
			<td><?php echo Html::anchor('manage/chainmaps/edit/'.$s->subGroupID, __('<i class="icon-edit"></i>&nbsp;Edit'),array('class' => 'btn btn-default btn-xs')); ?> <?php echo Html::anchor('manage/chainmaps/remove/'.$s->subGroupID, __('<i class="icon-trash"></i>&nbsp;Remove'),array('class' => 'btn btn-default btn-xs')); ?></td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php else: ?>
<p>No subgroups currently exist.</p>
<?php endif; ?>
