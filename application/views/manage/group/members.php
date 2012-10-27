<?php
$subgroups = $group->subgroups->find_all();

$hasSubgroups = false;
if( count( $subgroups->as_array() ) > 0 )
{
	$hasSubgroups = true;
}

?>
   <h2><?php echo __('Group Members') ?></h2>
			<p>The EVEID listed on this page are the ids that eve uses in game to reference your corp. You may find these from either your API info or sites such as http://evemaps.dotlan.net</p>
			<?php echo Html::anchor('manage/group/addMember', __('<i class="icon-plus-sign"></i>&nbsp;Add New Member'), array('class' => 'btn btn-primary pull-right') ); ?>
			<div class="clearfix"></div>
			<?php if( $hasSubgroups ): ?>
				 <?php foreach( $subgroups as $s ): ?>
					<h2 class="tableHeader"><?php echo $s->sgName ?></h2>
					<table class="table table-striped" width="100%">
						<tr>
							<th width="5%">Type</th>
							<th width="10%">EVE ID</th>
							<th width="70%">Access Name</th>
							<th width="15%">Options</th>
						</tr>
					<?php 
						$members = $group->groupmembers->where('subGroupID', '=', $s->subGroupID)->find_all();
						if( count( $members) > 0 ):
							foreach( $group->groupmembers->where('subGroupID', '=', $s->subGroupID)->find_all() as $m ): ?>
						<tr>
							<td><?php echo ucfirst($m->memberType) ?></td>
							<td><?php echo $m->eveID ?></td>
							<td><?php echo $m->accessName ?></td>
							<td><?php echo Html::anchor('manage/group/editMember/'.$m->id, __('<i class="icon-edit"></i>&nbsp;Edit')); ?> <?php echo Html::anchor('manage/group/removeMember/'.$m->id, __('<i class="icon-trash"></i>&nbsp;Remove')); ?></td>
						</tr>
						<?php endforeach ?>
						<?php else: ?>
						<tr>
							<td colspan='4'>No members</td>
						</tr>
						<?php endif; ?>
					</table>					
				<?php endforeach; ?>
					<h2 class='tableHeader'>Default</h2>
					<table class="table table-striped" width="100%">
						<tr>
							<th width="5%">Type</th>
							<th width="10%">EVE ID</th>
							<th width="70%">Access Name</th>
							<th width="15%">Options</th>
						</tr>
					<?php 
						$members = $group->groupmembers->where('subGroupID', '=', 0)->find_all();
						if( count( $members) > 0 ):
							foreach( $members as $m ): ?>
							<tr>
								<td><?php echo ucfirst($m->memberType) ?></td>
								<td><?php echo $m->eveID ?></td>
								<td><?php echo $m->accessName ?></td>
								<td><?php echo Html::anchor('manage/group/editMember/'.$m->id, __('<i class="icon-edit"></i>&nbsp;Edit')); ?> <?php echo Html::anchor('manage/group/removeMember/'.$m->id, __('<i class="icon-trash"></i>&nbsp;Remove')); ?></td>
							</tr>
						<?php endforeach ?>
						<?php else: ?>
						<tr>
							<td colspan='4'>No members</td>
						</tr>
						<?php endif; ?>
					</table>									
				
			<?php else: ?>
					<table class="table table-striped" width="100%">
						<tr>
							<th width="5%">Type</th>
							<th width="10%">EVE ID</th>
							<th width="70%">Access Name</th>
							<th width="15%">Options</th>
						</tr>
					<?php foreach( $group->groupmembers->find_all() as $m ): ?>
						<tr>
							<td><?php echo ucfirst($m->memberType) ?></td>
							<td><?php echo $m->eveID ?></td>
							<td><?php echo $m->accessName ?></td>
							<td><?php echo Html::anchor('manage/group/editMember/'.$m->id, __('<i class="icon-edit"></i>&nbsp;Edit')); ?> <?php echo Html::anchor('manage/group/removeMember/'.$m->id, __('<i class="icon-trash"></i>&nbsp;Remove')); ?></td>
						</tr>
					<?php endforeach ?>
					</table>
      <?php endif; ?>
