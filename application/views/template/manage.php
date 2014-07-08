<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>

    <!-- Le styles -->
    <link href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/css/admin.css" rel="stylesheet">
    <link href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="<?php echo URL::base(TRUE, TRUE);?>favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo URL::base(TRUE, TRUE);?>public/bootstrap/ico/apple-touch-icon-57-precomposed.png">


</head>
<body>

		<div class="navbar navbar-inverse navbar-fixed-top" style="margin: -1px -1px 0;">
		  <div class="navbar-inner">
			<div class="container" style="width: auto; padding: 0 20px;">
			  <a class="brand" href="#">siggy</a>
			  <ul class="nav">
				<li class="active"><a href="<?php echo URL::base(TRUE,TRUE); ?>manage">Admin</a></li>
			  </ul>
			  <p class="navbar-text pull-right">
				<a href="<?php echo URL::base(TRUE,TRUE); ?>">Back to scanning!</a>
			  </p>
			</div>
		  </div>
		</div>
		<div id='sidemenu'>
				<div class='well'>
						<div id='logoutBox'>
								<strong>Logged in as <?php echo Auth::$user->data['username']; ?> (<?php echo Html::anchor('account/logout', 'Log out'); ?>)</strong><br />
								<?php if( count($avaliableGroups) > 1 ): ?>
								<br />
								<form action='<?php echo URL::base(TRUE, TRUE);?>manage/admin/changeGroup' method='post'>
										<select name='group' onchange='submit();'>
										<?php 
											$selected = Auth::$user->data['groupID'];
											foreach( $avaliableGroups as $m ): ?>
											<option value="<?php echo $m['groupID']; ?>" <?php echo ( ($selected == $m['groupID']) ? "selected='seleced'" : ''); ?>><?php echo $m['groupName']; ?></option>
										<?php endforeach; ?>
										</select>
								</form>
								<?php endif; ?>
						</div>
						<ul class="nav nav-list">
							<li class="nav-header">Information</li>
							<li><?php echo Html::anchor('manage/group/dashboard', __('Announcements')); ?></li>
                            <?php if( Auth::$user->data['admin'] || $perms['can_view_logs'] ): ?>
							<li><?php echo Html::anchor('manage/logs/activity', __('Usage Logs')); ?></li>
							<li><?php echo Html::anchor('manage/logs/sessions', __('Active Sessions')); ?></li>
							<?php endif; ?>
							
                            <?php if( Auth::$user->data['admin'] || $perms['can_manage_group_members'] || $perms['can_manage_access'] ): ?>
							<li class="nav-header">Group Access</li>
							<?php endif; ?>
                            <?php if( Auth::$user->data['admin'] || $perms['can_manage_group_members'] ): ?>
							<li><?php echo Html::anchor('manage/group/members', __('Group Members')); ?></li>
							<li><?php echo Html::anchor('manage/group/subgroups', __('Subgroups')); ?></li>
							<?php endif; ?>
                            <?php if( Auth::$user->data['admin'] || $perms['can_manage_access'] ): ?>
							<li><?php echo Html::anchor('manage/access/configure', __('Management Access')); ?></li>
							<?php endif; ?>
                            
                            <?php if( Auth::$user->data['admin'] || $perms['can_manage_settings'] ): ?>
							<li class="nav-header">Group Settings</li>
							<li><?php echo Html::anchor('manage/settings/general', __('General')); ?></li>
							<li><?php echo Html::anchor('manage/settings/chain_map', __('Chain Map')); ?></li>
							<li><?php echo Html::anchor('manage/settings/statistics', __('Statistics')); ?></li>
							<?php endif; ?>
									
                            <?php if( Auth::$user->data['admin'] || $perms['can_view_financial'] ): ?>
							<li class="nav-header">Financial</li>
							<li><?php echo Html::anchor('manage/billing/overview', __('Billing Overview')); ?></li>
							<?php endif; ?>
							
							<?php if(Auth::$user->isAdmin()): ?>
							<li class="nav-header">Admin</li>
							<li><?php echo Html::anchor('manage/admin/groups', __('Groups')); ?></li>
							<?php endif; ?>				 
						</ul> 
				</div>
		</div>
     
     
     
     <div id='content'>
	  <div class="container-fluid">

		<div class="row-fluid">
    <?php
     // output messages
     if(Message::count() > 0) {
       echo '<div class="alert alert-info">';
       echo Message::output();
       echo '</div>';
     }
     ?>
				<?php echo $content; ?>
		</div>
		</div>
     </div>
   
</body>
</html>
