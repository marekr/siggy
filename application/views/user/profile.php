
   <h1><?php echo __('Account Details') ?></h1>
   <div class="content">

      <p>Username: <?php echo $user->username ?></p>
      <p>Email: <?php echo $user->email ?></p>
      <?php if( simpleauth::instance()->isGroupAdmin() ): ?>
			<p>Group manager: Yes</p>
			<?php else: ?>
			<p>Group manager: No</p>
			<?php endif; ?>
			<hr />
      <h2><b>Login Activity</b></h2>
      <p>Last login was <?php echo date('F jS, Y', $user->last_login) ?>, at <?php echo date('h:i:s a', $user->last_login) ?>.<br/>Total logins: <?php echo $user->logins ?></p>

   </div>
