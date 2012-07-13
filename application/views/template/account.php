<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" dir="ltr" lang="en-US">
<head>
   <title><?php echo $title ?></title>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
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
      
      #passError
      {    
				background: none repeat scroll 0 0 #910E0E;
				margin-bottom: 15px;
				padding: 4px;
      }
      
      input {
				padding: 2px;
				border: 1px solid #000;
      }
      
      input[type="submit"]
      {
				font-weight:bold;
      }
      
      label {
				font-weight:bold;
				margin-right: 5px;
      }
      
   .miniForm h2
      {
					font-size:1.05em;
					font-weight:bold;
					text-align:left;
					margin-bottom: 5px;
      }
      
      .miniForm li
      {
					clear: both;
					list-style:none;
					padding-bottom: 30px;
      }
      
      .miniForm ul:
      {
					margin-bottom:20px;
      }
      
      .miniForm label
      {
					width: 40%;
					text-align:left;
					float:left;
      }
      
      .miniForm input
      {
					float:left;
      }
      
      .miniForm input[type=submit]
      {
					float:none;
					margin-top: 10px;
      }      
      
      .miniForm ul,
      .miniform hr
      {
				clear:both;
      }
      
      .miniForm span.error
      {    
					background: #C50404;
					display: inline-block;
					font-size: 0.8em;
					margin-left: 55px;
					margin-top: 6px;
					padding: 3px;
      }
      
      .clear
      {
					clear:both;
      }
      
      a {
				 color: #FF7F00;
				font-weight: bold;
				text-decoration: underline;
      }      
      
      #characterSelect
      {
				width: 440px;
				margin: 0 auto;
      }
      
      #characterSelect input
      {
				border: 0;
      }
      
      #characterSelect li
      {
				float:left;
				padding:5px;
      }
      #characterSelect li:hover
      {
				background-color: #4D4D4D;
				cursor:pointer;
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
   <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
</head>
<body>
     <?php echo $content; ?>
</body>
</html>
