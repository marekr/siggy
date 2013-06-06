			<h2>Manage API Key</h2>
			<p>
					You must provide an proper working API key in order to use siggy. The following are requirements of a key:
					<ul>
						<li>Must be a new API Key(customizable keys).</li>
						<li>The following permissions are required:
							<ul>
								<li>CharacterInfo Public</li>
								<li>CharacterInfo Private*</li>
							</ul>
						</li>
						<li>Keys must be set to never expire or you will lose access immediately</li>
						<li>Please don't use API keys that do not belong to you</li>
					</ul>
			</p>
			<h3>API Keys</h3>
			<div class="pull-right">
			<?php echo Html::anchor('account/addAPI', __('<i class="icon-plus-sign"></i>&nbsp;Add API Key'), array('class' => 'btn btn-primary') ); ?>
			</div>
			<br />
			<br />
			<table class="table table-striped">
				<tr>
					<th width="15%">API ID</th>
					<th width="50%">API Key</th>
					<th>Status</th>
					<th width="20%">Actions</th>
				</tr>
				<?php foreach( $keys as $key ): ?>
				<tr>
					<td><?php echo $key['apiID']; ?></td>
					<td><?php echo $key['apiKey']; ?></th>
					<td><?php echo $key['status']; ?></th>
					<td><?php echo Html::anchor('account/editAPI/'.$key['entryID'], __('<i class="icon-edit"></i>&nbsp;Edit')); ?> <?php echo Html::anchor('account/removeAPI/'.$key['entryID'], __('<i class="icon-trash"></i>&nbsp;Remove')); ?></th>
				</tr>
				<?php endforeach; ?>
			</table>