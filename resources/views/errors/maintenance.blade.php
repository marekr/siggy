<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>siggy - Maintenance</title>

    <!-- Le styles -->
    <link href="<?php echo URL::base(TRUE, TRUE);?>bootstrap3/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo URL::base(TRUE, TRUE);?>css/frontend.css" rel="stylesheet">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="<?php echo URL::base(TRUE, TRUE);?>bootstrap3/js/bootstrap.min.js"></script>


    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="<?php echo URL::base(TRUE, TRUE);?>favicon.ico">
	<script type="text/javascript">
	$(document).ready(function() {
		$('.dropdown-toggle').dropdown()
	});
	</script>
</head>
<body>
	<div class="container">
		<div class="row text-center">
			<h1>siggy</h1>
			<img src="/images/siggy.png" height="180px" />
			<h2>Maintenance in progress</h2>
			<p>
				@if( empty($message) )
				siggy is currently undergoing maintenance related activities (upgrade, cleanup, etc). Please try again shortly.
				@else
				{{ $message }}
				@endif
			</p>
			Current version {{ SIGGY_VERSION }}
			<div class="panel text-left">
				<div class="panel-body">
					<div style="height:50vh;overflow-y:auto;">
						{!! $changelog !!}
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

