
    <div id="message" style='width:450px;'>
					<h1>Select a Character</h1>
					<div id="authBox">
							The following characters have been detected as having siggy access.
							<ul id='characterSelect'>
								<?php foreach($chars as $char): ?>
								<li>
									<form action='<?php echo URL::base(TRUE, TRUE);?>account/characterSelect' method='POST'>
										<input type='hidden' name='charID' value='<?php echo $char['charID']; ?>' />
										<!-- <img src='https://image.eveonline.com/Character/<?php echo $char['charID']; ?>_128.jpg' width='128' height='128'/> -->
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
    </div>