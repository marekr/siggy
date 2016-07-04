	<h3>
		Connected Characters
	</h3>
	<p>
			These are characters you connected to siggy via EVE SSO.
			<ul>
				<li>If the status of a character is "invalid", you must relogin via SSO on that character to allow siggy to regain access.</li>
			</ul>
	</p>
	<?php echo Html::anchor('account/connect', __('<i class="glyphicon glyphicon-plus"></i>&nbsp;Connect new character'), array('class' => 'btn btn-primary pull-right') ); ?>
	<br clear='all' />
	<br />
	<table class="table table-striped">
		<tr>
			<th width="10%">ID</th>
			<th width="40%">Name</th>
			<th width="40%">Permissions</th>
			<th width="10%">Actions</th>
		</tr>
		<?php foreach( $characters as $character ): ?>
		<tr>
			<td><?php echo $character_data[$character['character_id']]->id; ?></td>
			<td><?php echo $character_data[$character['character_id']]->name; ?></td>
			<td>characterLocationRead, characterNavigationWrite</td>
			<td><?php echo Html::anchor('account/disconnect/'.$character['character_owner_hash'], __('<i class="glyphicon glyphicon-remove"></i>&nbsp;Disconnect'), array('class' => 'btn btn-danger btn-xs')); ?></th>
		</tr>
		<?php endforeach; ?>
	</table>