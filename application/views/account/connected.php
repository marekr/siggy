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
			<th width="40%">Status</th>
			<th width="10%">Actions</th>
		</tr>
		<?php foreach( $characters as $character ): ?>
		<tr>
			<td><?php echo $character_data[$character->character_id]->id; ?></td>
			<td><?php echo $character_data[$character->character_id]->name; ?></td>
			<td>characterLocationRead, characterNavigationWrite</td>
			<td><?php echo $character->valid ? 'Ok' : 'Invalid' ?></td>
			<td>
				<form action="<?php echo URL::base(TRUE, TRUE);?>account/disconnect" method="post">
					<input type="hidden" name="_token" value="<?php echo Auth::$session->csrf_token;?>" />
					<input type="hidden" name="character_id" value="<?php echo $character_data[$character->character_id]->id; ?>" />
					<button class='btn btn-danger btn-xs' type="submit">Disconnect</button>
				</form>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>