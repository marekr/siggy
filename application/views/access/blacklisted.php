<div id="message">
	<div class="box" style="width:auto; ">
		<h3>Blacklisted</h3>
		<div>
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
accessMenu = new siggyMenu(
{	 
		ele: 'accessMenu', 
		dir: 'down',
		callback: function( id )
		{
			window.location.replace(  '<?php echo URL::base(true,true); ?>access/switch_membership/?k=' + id );
		},
		callbackMode: 'wildcard'
});

accessMenu.initialize();
</script>