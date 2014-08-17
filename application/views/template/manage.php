<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>

    <!-- Le styles -->
    <link href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap3/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo URL::base(TRUE, TRUE);?>public/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo URL::base(TRUE, TRUE);?>public/css/manage.css" rel="stylesheet">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap3/js/bootstrap.min.js"></script>
    <script src="<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.metisMenu.js"></script>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
	
	<script type="text/javascript">
	$(function() {

	//	$('#side-menu').metisMenu();

	});

	$(function() {
		$(window).bind("load resize", function() {
			width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
			if (width < 768) {
				$('div.sidebar-collapse').addClass('collapse')
			} else {
				$('div.sidebar-collapse').removeClass('collapse')
			}
		})
	})
	</script>
	

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="<?php echo URL::base(TRUE, TRUE);?>favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-57-precomposed.png">
</head>
<body>
	<nav class="navbar navbar-inverse navbar-fixed-top navbar-siggy" role="navigation" style="margin-bottom: 0">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">siggy</a>
		</div>
		<!-- /.navbar-header -->

		<ul class="nav navbar-top-links navbar-right">
			<li>
				<a href="<?php echo URL::base(TRUE,TRUE); ?>">Back to scanning!</a>
			</li>
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="fa fa-user fa-fw"></i> <?php echo Auth::$user->data['username']; ?> <i class="fa fa-caret-down"></i>
				</a>
				<ul class="dropdown-menu dropdown-user">
					<li>								
						<?php if( count($avaliableGroups) > 1 ): ?>
						<form action='<?php echo URL::base(TRUE, TRUE);?>manage/admin/changeGroup' method='post' style="padding: 6px;">
							<select name='group' class="form-control input-sm " onchange='submit();'>
							<?php 
								$selected = Auth::$user->data['groupID'];
								foreach( $avaliableGroups as $m ): ?>
								<option value="<?php echo $m['groupID']; ?>" <?php echo ( ($selected == $m['groupID']) ? "selected='seleced'" : ''); ?>><?php echo $m['groupName']; ?></option>
							<?php endforeach; ?>
							</select>
						</form>
						<?php endif; ?>
					</li>
					<li class="divider"></li>
					<li>
						<?php echo Html::anchor('account/logout', '<i class="fa fa-sign-out fa-fw"></i>Log out'); ?>
					</li>
				</ul>
				<!-- /.dropdown-user -->
			</li>
		</ul>
		<!-- /.navbar-top-links -->
		<div class="navbar-inverse navbar-static-side navbar-siggy" role="navigation">
			<div class="sidebar-collapse">
				<ul class="nav" id="side-menu">
					<li>
						<?php echo Html::anchor('manage/dashboard', __('<i class="fa fa-sitemap fa-fw"></i>Dashboard')); ?>
					</li>
					<?php if( Auth::$user->data['admin'] || $perms['can_manage_group_members'] || $perms['can_manage_access'] ): ?>
					<li class="active <?php echo (Request::initial()->controller() == "Group"?" active" : "") ?>">
						<a href="#"><i class="fa fa-key fa-fw"></i> Access<span class="fa arrow"></span></a>
						<ul class="nav nav-second-level">
							<?php if( Auth::$user->data['admin'] || $perms['can_manage_group_members'] ): ?>
							<li>
								<?php echo Html::anchor('manage/group/members', __('Group Members')); ?>
							</li>
							<li>
								<?php echo Html::anchor('manage/group/subgroups', __('Subgroups')); ?>
							</li>
							<?php endif; ?>
							<?php if( Auth::$user->data['admin'] || $perms['can_manage_access'] ): ?>
							<li>
								<?php echo Html::anchor('manage/access/configure', __('Management Access')); ?>
							</li>
							<?php endif; ?>
						</ul>
						<!-- /.nav-second-level -->
					</li>
					<li class="active <?php echo (Request::initial()->controller() == "Group"?" active" : "") ?>">
						<a href="#"><i class="fa fa-chain fa-fw"></i> Chainmaps<span class="fa arrow"></span></a>
						<ul class="nav nav-second-level">
							<li>
								<?php echo Html::anchor('manage/chainmaps/list', __('Manage')); ?>
							</li>
						</ul>
						<!-- /.nav-second-level -->
					</li>
					<?php endif; ?>
					<?php if( Auth::$user->data['admin'] || $perms['can_manage_settings'] ): ?>
					<li class="active <?php echo (Request::initial()->controller() == "Settings"?" active" : "") ?>">
						<a href="#"><i class="fa fa-wrench fa-fw"></i>Settings<span class="fa arrow"></span></a>
						<ul class="nav nav-second-level">
							<li>
								<?php echo Html::anchor('manage/settings/general', __('General')); ?>
							</li>
							<li>
								<?php echo Html::anchor('manage/settings/chain_map', __('Chain Map')); ?>
							</li>
							<li>
								<?php echo Html::anchor('manage/settings/statistics', __('Statistics')); ?>
							</li>
						</ul>
						<!-- /.nav-second-level -->
					</li>
					<?php endif; ?>
					<?php if( Auth::$user->data['admin'] || $perms['can_view_logs'] ): ?>
					<li class="active <?php echo (Request::initial()->controller() == "Logs"?" active" : "") ?>">
						<a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Activity<span class="fa arrow"></span></a>
						<ul class="nav nav-second-level">
							<li>
								<?php echo Html::anchor('manage/logs/activity', __('Usage Logs')); ?>
							</li>
							<li>
								<?php echo Html::anchor('manage/logs/sessions', __('Active Sessions')); ?>
							</li>
						</ul>
						<!-- /.nav-second-level -->
					</li>
					<?php endif; ?>
					<?php if( Auth::$user->data['admin'] || $perms['can_view_financial'] ): ?>
					<li class="active <?php echo (Request::initial()->controller() == "Billing"?" active" : "") ?>">
						<a href="#"><i class="fa fa-university fa-fw"></i>Financial<span class="fa arrow"></span></a>
						<ul class="nav nav-second-level">
							<li>
								<?php echo Html::anchor('manage/billing/overview', __('Billing Overview')); ?>
							</li>
						</ul>
						<!-- /.nav-second-level -->
					</li>
					<?php endif; ?>
				</ul>
				<!-- /#side-menu -->
			</div>
			<!-- /.sidebar-collapse -->
		</div>
		<!-- /.navbar-static-side -->
	</nav>
	<div id="page-wrapper">
		<?php if(Message::count() > 0): ?>
		<div class="row">
			<?php echo Message::output(); ?>
		</div>
		<?php endif; ?>
		
		<div class="row">
			<div class="col-lg-12">
				<?php echo $content; ?>
			</div>
		</div>
   </div>
</body>
</html>
