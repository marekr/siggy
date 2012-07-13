
    <div id="message">
					<h1>API Info</h1>
					<div id="authBox" class='miniForm'>
						<p style='font-weight:bold'>
								You must provide an proper working API key in order to use siggy. The API key <b>MUST</b> provide access to CharacterInfo public AND private AND set to not expire.
								You must use the customizable API keys avaliable at <a href='https://support.eveonline.com/api'>https://support.eveonline.com/api</a>, the old limited and full keys are not accepted.
								<br />
						</p>
						<br />
						<form action='<?php echo URL::base(TRUE, TRUE);?>account/setAPI' method='POST'>
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
							<h2>API Key Status</h2>
							<div style='text-align:left'>
									<strong>State: 
								<?php if( $status == 'missing'): ?>
										Nothing currently set
								<?php elseif ($status == 'invalid'): ?>
										Invalid Details
								<?php elseif ($status == 'failed'): ?>
										Failed more than 3 times, this may be due to invalid key permissions.
								<?php else: ?>
										Set and valid
								<?php endif; ?>
								</strong> 
							</div>
							<br />
							<h2>API Details</h2>
							<ul>
								 <li>
										<label>API ID</label>
										<?php echo $form->input('apiID'); ?>
								 </li>
								 <li>
										<label>API Key/Verification code</label>
										<?php echo $form->input('apiKey') ?>
								 </li>
							</ul>
							<div class='clear'></div>
							<input type='submit' name='set' value='Set' />
							<?php if( $status != 'missing' ): ?>
							<br />
							<br />
							or<br />
							<br />
							<a href="<?php echo URL::base(TRUE, TRUE);?>">Cancel</a>
							<?php endif; ?>
						</form>
          </div>
    </div>