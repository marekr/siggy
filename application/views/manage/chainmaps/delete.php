
<h1>Confirm deletion</h1>
<div class="content">
<form action="<?php echo URL::base(true,true);?>manage/chainmaps/remove/<?php echo $id; ?>" method="POST">
	<input type="hidden" value="1" name="confirm" />
	<p>Are you sure you want to remove the sub group '<?php echo $data['sgName']; ?>'? All members will be put back in the default sub group.</p>

	<button type="submit" class="btn">Confirm Deletion</button>
	<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
</form>