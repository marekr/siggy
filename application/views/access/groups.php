<div id="message">
	<div class="box" style="width:auto;">
		<h3>Select a group</h3>
		<div>
			<ul id='character-select'>
			<?php foreach($groups as $group): ?>
				<li onClick="javascript:$(this).find('form').submit();">
					<form action='<?php echo URL::base(TRUE, TRUE);?>access/groups' method='POST'>
						<input type='hidden' name='group_id' value='<?php echo $group->id; ?>' />
						<?php echo $group->name; ?>
					</form>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>

<script type='text/javascript'>
$('#activity-siggy').show();
</script>