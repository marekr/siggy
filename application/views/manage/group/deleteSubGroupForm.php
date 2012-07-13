
		<h1>Confirm deletion</h1>
   <div class="content">
<?php
$form = new Appform();

echo $form->open('manage/group/removeSubGroup/'.$id);
echo $form->hidden('confirm', '1');
?>
   <p>Are you sure you want to remove the sub group '<?php echo $data['sgName']; ?>'? All members will be put back in the default sub group.</p>
<?php 
	echo $form->submit(NULL, __('Confirm'));
echo $form->close();
?>
   </div>