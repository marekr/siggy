<div class="well">
	<h2>Getting a siggy group</h2>
	
	<p>siggy is avaliable for any corporation or alliance to utilize as a service. It is maintained neutrally and is trusted by several large alliances and corporations.
	Every user gets the same feature and nobody gets special treatment or access.</p>
	<p>siggy has a concept of "groups". A group can consist of any corporation or characters you add manually in a management control panel.
	There is no limitations on the number of members or type. You can add just characters if you desire. </p>
	<p>Billing is based on a rolling balance system. You deposit isk and the system automatically subtracts a daily charge each day. The daily charge comes out to
	approximately equal 1 mil ISK per member per month at a base of 10 members. The cost per member decreases as you have more members in the assumption bigger corps have more alts.
	For more information on costs <a href="<?php echo URL::base(TRUE, TRUE);?>pages/costs/">click here</a></p>
	<?php if( Auth::loggedIn() && Auth::$user->data['groupID'] == 0 ): ?>
	<div class="text-centered">
		<a href="<?php echo URL::base(TRUE, TRUE);?>pages/createGroup/" class="btn btn-large btn-primary text-centered">Still interested? Continue to group creation</a>
	</div>
	<?php elseif( !Auth::loggedIn() ): ?>
	<div class="text-centered">
		<a href="<?php echo URL::base(TRUE, TRUE);?>account/login/" class="btn btn-large btn-primary text-centered">Login</a>
		<br />
		<p>
			then select "Create Group" from the upper menu bar.
		</p>
	</div>
	<?php endif; ?>
</div>