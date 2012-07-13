
    <div id="message">
					<h1>Register</h1>
					<div id="authBox" class='miniForm'>
						<p>To create an account, please fill in the info requested below. All passwords are salted when stored and are secued, the email address is mainly for the forgot password feature. Username,password and email<b>DO NOT</b> have to be the same as your eve account. You will be asked for an API key on a later page</p>
						<form action='<?php echo URL::base(TRUE, TRUE);?>account/register' method='POST'>
<?php
$form = new Appform();
if(isset($errors)) {
   $form->errors = $errors;
}
if(isset($defaults)) {
   $form->defaults = $defaults;
} else {
   unset($_POST['password']);
   unset($_POST['password_confirmation']);
   $form->defaults = $_POST;
}
?>						
							<br />
							<ul>
								 <li>
										<label>Username</label>
										<?php echo $form->input('username'); ?>
								 </li>
								 <li>
										<label>Email</label>
										<?php echo $form->input('email') ?>
								 </li>
								 <li>
										<label>Password</label>
										<?php echo $form->password('password') ?>
								 </li>
								 <li>
										<label>Confirm Password</label>
										<?php echo $form->password('password_confirm') ?>
								 </li>
							</ul>
							<div class='clear'></div>
							<input type='submit' name='register' value='Register' />
						</form>
          </div>
    </div>