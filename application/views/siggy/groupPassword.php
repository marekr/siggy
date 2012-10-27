
       <style type='text/css'>
body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,fieldset,input,textarea,p,blockquote,th,td { 
	margin:0;
	padding:0;
}
table {
	border-collapse:collapse;
	border-spacing:0;
}
fieldset,img { 
	border:0;
}
address,caption,cite,code,dfn,em,strong,th,var {
	font-style:normal;
	font-weight:normal;
}
ol,ul {
	list-style:none;
}
caption,th {
	text-align:left;
}
h1,h2,h3,h4,h5,h6 {
	font-size:100%;
	font-weight:normal;
}
q:before,q:after {
	content:'';
}
abbr,acronym { border:0;
}
    
    
      body {
        background: #111111 url(<?php echo URL::base(TRUE, TRUE);?>public/images/bg.jpg) repeat-x;
        font-family: Arial;
        font-size:13px;
        color: white;
      }
      
      #message
      {
        width: 30%;
        margin: 200px auto 0;
      }
      
      #message h1
      {
        font-weight:bold;
        font-size:1.3em;
      }
      
      #authBox
      {
				background: #000;
				text-align:center;
				padding:20px;
      }
      
      #passError
      {    
				background: none repeat scroll 0 0 #910E0E;
				margin-bottom: 15px;
				padding: 4px;
      }
      </style>

    <div id="message">
    <h1>Authenication Required</h1>
    <?php if( $two=1 ): ?>
      
      <?php if( $trusted ): ?>
				<?php if($groupData['groupID'] != 0 ): ?>
					<div id="authBox">
						<p style='font-weight:bold'>
						In order to continue, please enter your group's access password below. This password should have been provided by your group in a bulletin,mail,w/e.<br /><br />
						<?php if( $wrongPass == true ): ?>
							<p id="passError">You have entered an incorrect password!</p>
						<?php endif; ?>
						<form action='<?php echo URL::base(TRUE, TRUE);?>/doGroupAuth' method='POST'>
							<input type='password' name='authPassword' /><br /><br />
							<input type='submit' value='Submit' style="padding:10px" />
						</form>
          </div>
				<?php else: ?>
				Why are you here?
        <?php endif; ?>
      <?php else: ?>
        Please accept the trust request and refresh the page afterwards to continue.
      <?php endif; ?>
      
    <?php else: ?>
      Sorry, you must be using the EVE in game browser to continue.
    <?php endif; ?>
    </div>
    <script type='text/javascript'>
    	accessMenu = new siggyMenu(
	{	 
			ele: 'accessMenu', 
			dir: 'down',
			callback: function( id )
			{
				window.location.replace(  '<?php echo URL::base(true,true); ?>doswitchMembership/?k=' + id );
			},
			callbackMode: 'wildcard'
	});
	
	accessMenu.initialize();
	</script>