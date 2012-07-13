<?php
$form = new Appform();
if(isset($errors)) {
   $form->errors = $errors;
}
if(isset($data)) {
   unset($data['password']);
   $form->values = $data;
}
echo $form->open('user/profile_edit');
?>
   <h1><?php echo __('Edit Account'); ?></h1>
   <div class="content">
   <ul>
      <li><label><?php echo __('Username'); ?></label></li>
      <?php echo $form->input('username', null, array('info' => __('Length between 4-32 characters. Letters, numbers, dot and underscore are allowed characters.'))); ?>
      <li><label><?php echo __('Email address'); ?></label></li>
      <?php echo $form->input('email') ?>
      <li><label><?php echo __('Password'); ?></label></li>
      <?php echo $form->password('password', null, array('info' => __('Password should be between 6-42 characters.'))) ?>
      <li><label><?php echo __('Re-type Password'); ?></label></li>
      <?php echo $form->password('password_confirm') ?>
   </ul>
   <br>
<?php
echo $form->submit(NULL, __('Save'));
echo $form->close();
?>
   </div>