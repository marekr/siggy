
<h3>Character Select</h3>
	<?php if(count($selectableChars) < 1 && count($unselectableChars) < 1 ): ?>
	<div class="well">
		<h4>No characters </h4>
		<p>If you wish for these characters to become selectable, they must be part of a siggy group. You must either have your siggy admin add your character to the group OR 
		create a group if you do not have one. <a href="http://wiki.siggy.borkedlabs.com/getting-siggy">http://wiki.siggy.borkedlabs.com/getting-siggy</a>
	</div>
	<?php endif; ?>
	<?php if(count($selectableChars)): ?>
	<div class="well">
		<h4>Selectable Characters </h4>
		<p>
			The following linked characters have been detected as having siggy access.<br />
			Click one to continue.<br /><br />
		</p>

		<ul id='character-select'>
			<?php foreach($selectableChars as $char): ?>

			<li <?php echo ($char->id == $selectedCharID ? 'class="selected"' : ''); ?> onClick="javascript:$(this).find('form').submit();">
				<form action='<?php echo URL::base(TRUE, TRUE);?>account/characters' method='POST'>
					<input type='hidden' name='charID' value='<?php echo $char->id; ?>' />

					<input type='image' src='https://image.eveonline.com/Character/<?php echo $char->id; ?>_64.jpg' name='select' width='64' height='64' alt='<?php echo $char->name; ?>' />
					<input type='image' src='https://image.eveonline.com/Corporation/<?php echo $char->corporation_id; ?>_64.png' name='select' width='64' height='64' alt='<?php echo $char->corporation()->name; ?>' />
					<div class='details'>
						<b><?php echo $char->name; ?></b>
						<br />
						<?php echo $char->corporation()->name; ?>
					</div>
				</form>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
	<?php if(count($unselectableChars)): ?>
	<div class="well">
		<h4>Unselectable Characters </h4>
		<p>
			The following linked characters are linked to your account <strong>BUT DO NOT HAVE ACCESS</strong>
		</p>
		<ul id='character-select'>
			<?php foreach($unselectableChars as $char): ?>
			<li class='disabled'>
					<img src='https://image.eveonline.com/Character/<?php echo $char->id; ?>_64.jpg' name='select' width='64' height='64' alt='<?php echo $char->name; ?>' />
					<img src='https://image.eveonline.com/Corporation/<?php echo $char->corporation_id; ?>_64.png' name='select' width='64' height='64' alt='<?php echo $char->corporation()->name; ?>' />
					<div class='details'>
						<b><?php echo $char->name; ?></b>
						<br />
						<?php echo $char->corporation()->name; ?>
					</div>
				</form>
			</li>
			<?php endforeach; ?>
		</ul>
		
		<br clear='all' />
		<p>If you wish for these characters to become selectable, they must be part of a siggy group. You must either have your siggy admin add your character to the group OR 
		create a group if you do not have one. <a href="http://wiki.siggy.borkedlabs.com/getting-siggy">http://wiki.siggy.borkedlabs.com/getting-siggy</a>
	</div>
	<?php endif; ?>
