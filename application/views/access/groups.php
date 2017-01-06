<div class="box">
	<div class="box-header">Select a group to access</div>
	<ul class='selection-list'>
	<?php foreach($groups as $group): ?>
		<li onClick="javascript:$(this).find('form').submit();">
			<form action='<?php echo URL::base(TRUE, TRUE);?>access/groups' method='POST'>
				<input type="hidden" name="_token" value="<?php echo Auth::$session->csrf_token;?>" />
				<input type='hidden' name='group_id' value='<?php echo $group->id; ?>' />
				<?php if($group->password_required): ?>
					<i class="fa fa-lock" aria-hidden="true"></i>
				<?php endif; ?>
				<div class="details">
					<b><?php echo $group->name; ?></b>
				</div>
			</form>
		</li>
	<?php endforeach; ?>
	</ul>
</div>


<script type='text/javascript'>
$('#activity-siggy').show();
</script>