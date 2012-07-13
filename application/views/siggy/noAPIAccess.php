
    <div id="message" style='width:450px;'>
					<h1>Important Message</h1>
					<div id="authBox">
							<?php if( $messageType == 'selectChar' ): ?>
							This is your first time using the API authed login, you must pick a character that has the desired access on your account.<br />
							<a href='<?php echo URL::base(TRUE, TRUE);?>account/characterSelect' class='fauxButton'>Select a character</a>
							<?php elseif( $messageType == 'noAccess' ): ?>
							You do not currently have an character selected that has siggy access.<br />
							<a href='<?php echo URL::base(TRUE, TRUE);?>account/characterSelect' class='fauxButton'>Change character selection</a>
							<?php elseif( $messageType == 'missingAPI' ): ?>
								You must enter an valid API key to continue.<br />
							<a href='<?php echo URL::base(TRUE, TRUE);?>account/setAPI' class='fauxButton'>Change API Key</a>
							<?php elseif( $messageType == 'badAPI' ): ?>
								The API Key you have provided is either invalid or is not providing the proper permissions to the desired API info.<br />
							<a href='<?php echo URL::base(TRUE, TRUE);?>account/setAPI' class='fauxButton'>Change API Key</a>
							<?php endif; ?>
          </div>
    </div>