<div id="message">
	<div class="box" style="width:auto;">
		<div class="box-header">Blacklisted</div>
		<div class="box-content">
			<p>
				Your character has been blacklisted from <b><?php echo $groupName; ?></b>
				<?php if( !empty($reason) ): ?>
				with reason:
				<b><?php echo $reason; ?></b>
				<?php endif; ?>
			</p>
			<p>
				If you think this is in error, contact your siggy group manager (usually corp CEO).
			</p>
			<br />
			<h4>IMPORTANT NOTE:</h4>
			<p>You are only blacklisted from this siggy group, you are still able to use siggy with other groups.</p>
		</div>
	</div>
</div>

<script type='text/javascript'>
$('#activity-siggy').show();
</script>