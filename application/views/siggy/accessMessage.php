<html>
  <head>
    <title>siggy</title>
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
        border: 1px solid #2B2A2A;
      }
      
      #message h1
      {
        font-weight:bold;
        font-size:1.1em;
        padding: 8px;
        background: #171717;
      }
      

      #authBox
      {
				background: #000;
				text-align:center;
				padding:20px;
      }
      .fauxButton
      {
				background: #ED7D00;
				color: #fff;
				border: 2px solid #D0D0D0;
				text-decoration: none;
				padding:5px;
				margin-top:10px;
				display:inline-block;
				font-weight:bold;
      }            
      </style>
      <script type='text/javascript'>
        if( typeof(CCPEVE) != "undefined" )
        {
          CCPEVE.requestTrust('http://siggy.borkedlabs.com/*');
        }      
      </script>
  </head>

  <body>
    <div id="message">
    <h1>Welcome to siggy!</h1>
			<div id="authBox">
    <?php if( strpos($_SERVER['HTTP_USER_AGENT'], 'EVE-IGB') ): ?>
      <?php if( (isset($groupData['groupID']) && $groupData['groupID'] == 0) && $trusted ): ?>
        Sorry, but you currently do not have access to siggy. <br />
        <?php if( isset($_SERVER['HTTP_EVE_CORPID']) ): ?>
				<br />Corporation ID: <?php echo $_SERVER['HTTP_EVE_CORPID']; ?><br />
				Character ID: <?php echo $_SERVER['HTTP_EVE_CHARID']; ?><br />
        <?php endif; ?>
      <?php else: ?>
        Please accept the trust request and refresh the page afterwards to continue.
        <br />
        <br />
        <br />
        <a href="http://siggy.borkedlabs.com" target="_blank">
        <div class='fauxButton'>
					Site trusted? CCP BEING TERRIBLE? Try this button.
				</div>
				</a>
      <?php endif; ?>
		</div>
	</div>
      
    <?php else: ?>
      Sorry, you must be using the EVE in game browser to continue.
    <?php endif; ?>
    <?php if( defined('MESSDEBUG') ): ?>
        <pre>
        <?php print_r($_SERVER); ?>
				</pre>
		<?php endif; ?>
  </body>
</html>