
    <div id="message">
					<h1>Forgot Password</h1>
					<div id="authBox" class='miniForm'>
						<p>Please enter the email address of the account you are trying to recover.</p>
						<form action='<?php echo URL::base(TRUE, TRUE);?>account/forgotPassword' method='POST'>
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
										<label>Email</label>
										<?php echo $form->input('reset_email'); ?>
								 </li>
							</ul>
							<div class='clear'></div>
							<input type='submit' name='send' value='Confirm Change' />
						</form>
          </div>
    </div>