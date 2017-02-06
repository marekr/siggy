
   <h1><?php echo ___('Groups') ?></h1>
   <div class="content">
			<div class="info">The EVEID listed on this page are the ids that eve uses in game to reference your corp. You may find these from either your API info or sites such as http://evemaps.dotlan.net</div>
			<?php echo Html::anchor('manage/admin/addGroup', ___('Add New Group')); ?>
			
					<table class="table table-striped" width="100%">
						<tr>
							<th width="10%">Ticker</th>
							<th width="75%">Name</th>
							<th width="15%">Next Payment Date</th>
							<th width="15%">Options</th>
						</tr>
					<?php foreach( $groups as $m ): ?>
						<tr>
							<td><?php echo $m->groupTicker ?></td>
							<td><?php echo $m->groupName ?></td>
							<td><?php echo ($m->billable) ? ($m->lastPayment + 60*60*24*($m->payedForDays) ) : '--'; ?></td>
							<td><?php echo Html::anchor('manage/admin/groupBill/'.$m->groupID, ___('Bill')); ?></td>
						</tr>
					<?php endforeach ?>
					</table>
	</div>
