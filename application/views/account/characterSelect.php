
		<h3>Character Select</h3>
		<?php if( $apiError ): ?>
		<div class="alert alert-danger">
		  <h5>Error</h5> There was an issue obtaining your character information from the EVE API. Please ensure your key is correct or try again another time.
		</div>
		<?php else: ?>
			<?php if(count($chars)): ?>
			<p>
				The following characters have been detected as having siggy access on the provided API key.
			</p>

			<div class="well">
					<ul id='characterSelect'>
						<?php foreach($chars as $char): ?>
						<li <?php echo ($char['charID'] == $selectedCharID ? 'class="selected"' : ''); ?>>
							<form action='<?php echo URL::base(TRUE, TRUE);?>account/characterSelect' method='POST'>
								<input type='hidden' name='charID' value='<?php echo $char['charID']; ?>' />
								<input type='image' src='https://image.eveonline.com/Character/<?php echo $char['charID']; ?>_128.jpg' name='select' width='128' height='128' alt='<?php echo $char['name']; ?>' />
								<br />
								<b><?php echo $char['name']; ?></b><br />
								<?php echo $char['corpName']; ?>
							</form>
						</li>
						<?php endforeach; ?>
					</ul>
					
					<br clear='all' />
			</div>
			<?php else: ?>
			<div class="alert alert-danger">Sorry, you have no characters with access on your API key. If your corporation or character is already supposed to have access, verify your API key is not expired and has your character included, otherwise contact the person in charge. If you want siggy but don't have access, read the guide here: <a href="http://wiki.siggy.borkedlabs.com/getting_siggy">http://wiki.siggy.borkedlabs.com/getting_siggy</a></div>
			<?php endif; ?>
		<?php endif; ?>