	<h3>Manage API Key</h3>
	<p>
			You must provide an proper working API key in order to use siggy. The following are requirements of a key:
			<ul>
				<li>Must be a new API Key(customizable keys).</li>
				<li>No permissions are required, except you must either set it to include the character with access or all characters</li>
				<li>Keys must be set to never expire or you will lose access</li>
				<li>Please don't use API keys that do not belong to you</li>
			</ul>
	</p>
	<h3>
		API Keys
		<?php echo Html::anchor('account/addAPI', __('<i class="icon-plus-sign"></i>&nbsp;Add API Key'), array('class' => 'btn btn-primary pull-right') ); ?>
	</h3>
	<br />
	<table class="table table-striped">
		<tr>
			<th width="10%">API ID</th>
			<th width="50%">API Key</th>
			<th>Status</th>
			<th width="25%">Actions</th>
		</tr>
		<?php foreach( $keys as $key ): ?>
		<tr>
			<td><?php echo $key['apiID']; ?></td>
			<td><?php echo $key['apiKey']; ?></th>
			<td><?php echo $key['status']; ?></th>
			<td><?php echo Html::anchor('account/editAPI/'.$key['entryID'], __('<i class="icon-edit"></i>&nbsp;Edit'), array('class' => 'btn btn-primary btn-xs')); ?>
			<?php echo Html::anchor('account/removeAPI/'.$key['entryID'], __('<i class="icon-trash"></i>&nbsp;Remove'), array('class' => 'btn btn-danger btn-xs')); ?></th>
		</tr>
		<?php endforeach; ?>
	</table>