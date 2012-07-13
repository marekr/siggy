
    <div id="message">
					<h1>Change Password</h1>
					<div id="authBox" class='miniForm'>
						<p>Please enter your current password and then enter and confirm your new desired password.</p>
						<form action='<?php echo URL::base(TRUE, TRUE);?>account/changePassword' method='POST'>
<?php
$form = new Appform();
if(isset($errors)) {
   $form->errors = $errors;
}
if(isset($defaults)) {
   $form->defaults = $defaults;
} else {
   unset($_POST['current_password']);
   $form->defaults = $_POST;
}
?>						
							<br />
							<ul>
								 <li>
										<label>Current Password</label>
										<?php echo $form->password('current_password'); ?>
								 </li>
								 <li>
										<label>New Password</label>
										<?php echo $form->password('password') ?>
								 </li>
								 <li>
										<label>Confirm New Password</label>
										<?php echo $form->password('password_confirm') ?>
								 </li>
							</ul>
							<div class='clear'></div>
							<input type='submit' name='confirm' value='Confirm Change' />
							<br />
							<br />
							or<br />
							<br />
							<a href="<?php echo URL::base(TRUE, TRUE);?>">Cancel</a>
						</form>
          </div>
    </div>