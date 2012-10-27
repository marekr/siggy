
      <script type='text/javascript'>
        if( typeof(CCPEVE) != "undefined" )
        {
          CCPEVE.requestTrust('<?php echo URL::base(true,true); ?>*');
        }      
      </script>
<div class="well">
	<h2>Welcome to siggy!</h2>
	<?php if( $igb && $trusted  ): ?>
		<?php if( (isset($groupData['groupID']) && $groupData['groupID'] == 0)  ): ?>
		Sorry, but you currently do not have access to siggy. <br />
			<?php if( isset($_SERVER['HTTP_EVE_CORPID']) ): ?>
					<br />Corporation ID: <?php echo $_SERVER['HTTP_EVE_CORPID']; ?><br />
					Character ID: <?php echo $_SERVER['HTTP_EVE_CHARID']; ?><br />
			<?php endif; ?>
		<?php endif; ?>
	<?php elseif ( $igb && !$trusted ): ?>
		<p>Please accept the trust request and click the button to continue:</p>
		<div class="text-centered">
			<a href="<?php echo URL::base(true,true); ?>" class="btn btn-primary btn-large">Continue</a>
		</div>
	<?php endif; ?>
</div>