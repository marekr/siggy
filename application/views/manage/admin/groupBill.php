
   <h1><?php echo ___('Groups') ?></h1>
   <div class="content">
			<div class="info">Boo</div>
			
					<table class="content" width="100%">
						<tr>
							<th width="10%">Access Type</th>
							<th width="80%">Access Ticker</th>
							<th width="10%">Count</th>
						</tr>
					<?php foreach( $members as $m ): ?>
						<tr>
							<td><?php echo $m['memberType']; ?></td>
							<td><?php echo $m['accessName'];?></td>
							<td><?php echo $m['userCount'];?></td>
						</tr>
					<?php endforeach ?>
						<tr>
							<td colspan='2'>Total Members</td>
							<td><?php echo $totalMembers;?></td>
						</tr>
						<tr>
							<td colspan='2'>Total Cost</td>
							<td><?php echo $cost;?> mil isk</td>
						</tr>
					</table>
   </div>
