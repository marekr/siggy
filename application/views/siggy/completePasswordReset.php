
    <div id="message">
					<h1>Complete Password Reset</h1>
					<div id="authBox" class='miniForm'>
						<p>Please enter your account's email address and the reset token provided in the email sent to you.</p>
						<form action='<?php echo URL::base(TRUE, TRUE);?>account/completePasswordReset' method='POST'>
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
										<label>Account Email</label>
										<?php echo $form->input('reset_email') ?>
								 </li>
								 <li>
										<label>Reset Token</label>
										<?php echo $form->password('reset_token'); ?>
								 </li>
							</ul>
							<div class='clear'></div>
							<input type='submit' name='proceed' value='Proceed with Reset' />
						</form>
          </div>
    </div>