<?php 
	$subgroups = $group->subgroups->find_all();
?>
   <h1><?php echo __('Subgroups') ?></h1>
			<div class="info">Subgroups are a <b>COMPLETELY OPTIONAL</b> system using which you can separate your group members. For now it cuases  separate system lists(for now that's the only functionality) to display. 
			Subgroups will still be able to see all the sigs of the group as a whole. This comes in handy when living in separate holes or whatever bizarre use one may find.</div>

			<?php echo Html::anchor('manage/group/addSubGroup', __('<i class="icon-plus-sign"></i>&nbsp;Add New Sub Group'), array('class' => 'btn btn-primary pull-right') ); ?>
			<div class="clearfix"></div>
			<br />			
<?php if( count( $subgroups->as_array() ) > 0 ): ?>
			<table class="table table-striped" width="100%">
				<tr>
					<th width="70%">Subgroup</th>
					<th width="15%"># Members</th>
					<th width="15%">Options</th>
				</tr>
      <?php foreach( $subgroups as $s ): ?>
				<tr>
					<td><?php echo $s->sgName ?></td>
					<td><?php echo $s->groupmembers->count_all(); ?></td>
					<td><?php echo Html::anchor('manage/group/editSubGroup/'.$s->subGroupID, __('<i class="icon-edit"></i>&nbsp;Edit')); ?> <?php echo Html::anchor('manage/group/removeSubGroup/'.$s->subGroupID, __('<i class="icon-trash"></i>&nbsp;Remove')); ?></td>
				</tr>
      <?php endforeach ?>
      </table>
 <?php else: ?>
	<p>No subgroups currently exist.</p>
 <?php endif; ?>
<?php // echo View::factory('profiler/stats'); ?>
