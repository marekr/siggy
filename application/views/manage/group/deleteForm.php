
		<h1>Confirm deletion</h1>
   <div class="content">
<?php
$form = new Appform();

echo $form->open('manage/group/removeMember/'.$id);
echo $form->hidden('confirm', '1');
?>
   <p>Are you sure you want to remove '<?php echo $data['accessName']; ?>' from the group access?</p>
<?php 
	echo $form->submit(NULL, __('Confirm'));
echo $form->close();
?>
   </div>