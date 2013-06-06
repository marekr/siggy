
    <div id="message" style='width:450px;'>
					<h1>Important Message</h1>
					<div id="authBox">
							<?php if( $messageType == 'selectChar' ): ?>
							Please select a character for use with access.<br /><br />
							<a href='<?php echo URL::base(TRUE, TRUE);?>account/characterSelect' class='btn'>Select a character</a>
							<?php elseif( $messageType == 'missingAPI' ): ?>
							You must add a valid API key to continue.<br /><br />
							<a href='<?php echo URL::base(TRUE, TRUE);?>account/apiKeys' class='btn'>Manage API Keys</a>
							<?php elseif( $messageType == 'badAPI' ): ?>
							The character you have selected no longer has a valid API key.<br /><br />
							<a href='<?php echo URL::base(TRUE, TRUE);?>account/apiKeys' class='btn'>Manage API Keys</a>
							<?php else: ?>
							You do not currently have an character selected that has siggy access.<br /><br />
							<a href='<?php echo URL::base(TRUE, TRUE);?>account/characterSelect' class='btn'>Change character selection</a>
							<?php endif; ?>
          </div>
    </div>