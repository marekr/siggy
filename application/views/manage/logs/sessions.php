<h3>Active Sessions (last 30 minutes)</h3>
<p>This page shows all access that has occurred in the last 30 minutes. Data is organized by the reported character along with any additional information.</p>
<p>Please note, if you change the group password or remove someones access, they will still show as an active session but they will no longer be updating.</p>

<table class="table table-striped">
	<thead>
		<tr>
			<th width="5%"></th>
			<th width="10%">Char. Name</th>
			<th width="10%">ID</th>
			<th width="75%">Details</th>
		</tr>
	</thead>
	
	<?php if( count($sessions) > 0 ): ?>
	<?php foreach($sessions as $sess): ?>
	<tr>
		<td><img src="http://image.eveonline.com/Character/<?php echo $sess['charID']; ?>_32.jpg" width="32" height="32" /> </td>
		<td>
			<?php echo $sess['charName']; ?>
			</td>
		<td><?php echo $sess['charID']; ?></td>
		<td>
		<table class="table table-condensed">
			<thead>
				<tr>
					<th width="10%">Session Type</th>
					<th width="10%">Account ID</th>
					<th width="15%">Chainmap</th>
					<th width="15%">Time created</th>
					<th width="15%">IP Address</th>
					<th width="15%">Last update</th>
				</tr>
			</thead>
			<?php foreach($sess['data'] as $d): ?>
			<tr>
				<td>
				<?php if( $d['sessionType'] == 'siggy' ): ?>
				siggy account
				<?php elseif( $d['sessionType'] == 'sso' ): ?>
				EVE SSO
				<?php else: ?>
				IGB
				<?php endif; ?>
				</td>
				<td><?php echo $d['userID']; ?></td>
				<td><?php echo $d['chainmap_name']; ?></td>
				<td><?php echo date("d/m/y g:m", $d['created']); ?></td>
				<td>
				<?php echo $d['ipAddress']; ?>
				</td>
				<td><?php echo date("d/m/y g:m", $d['lastBeep']); ?></td>
			</tr>
			<?php endforeach; ?>
        </table>
		
		
		</td>
	</tr>
	<?php endforeach; ?>
	<?php endif; ?>
</table>