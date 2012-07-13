<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" dir="ltr" lang="en-US">
<head>
   <title><?php echo $title ?></title>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
   <?php foreach ($styles as $file => $type) echo HTML::style($file, array('media' => $type)), "\n" ?>
   <?php foreach ($scripts as $file) echo HTML::script($file), "\n" ?>
   <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
</head>
<body>
     <?php if( !simpleauth::instance()->logged_in()): ?>
   <div id="page_centered">
   
    <?php
     // output messages
     if(Message::count() > 0) {
       echo '<div class="content">';
       echo Message::output();
       echo '</div>';
     }
     ?>
     
     <?php echo $content; ?>
     <?php else: ?>
   <div id="page">
     
     <div id='left'>
				 <div id='header'>
						siggy
				 </div>
				<div id='logoutBox'>Logged in as <?php echo simpleauth::instance()->get_user()->username; ?> (<?php echo Html::anchor('user/logout', 'Log out'); ?>)
				 <?php if(simpleauth::instance()->isAdmin()): ?><br />
				 <form action='<?php echo URL::base(TRUE, TRUE);?>manage/admin/changeGroup' method='post'>
				<select name='group' onchange='submit();'>
					<?php 
					$groups = ORM::factory('group')->find_all();
					$selected = simpleauth::instance()->get_user()->groupID;
					foreach( $groups as $m ): ?>
					<option value="<?php echo $m->groupID; ?>" <?php echo ( ($selected == $m->groupID) ? "selected='seleced'" : ''); ?>><?php echo $m->groupName; ?></option>
					<?php endforeach; ?>
				</select>
				</form>
				 <?php endif; ?></div>
				 <ul id='nav'>
				 <?php if(simpleauth::instance()->isGroupAdmin()): ?>
					 <li class='menu selected'>
						 <h3 style="">Group Members</h3>
						 <ul>
							<li><?php echo Html::anchor('manage/group', __('Group Members')); ?></li>
							<li><?php echo Html::anchor('manage/group/subgroups', __('Subgroups')); ?></li>
						 </ul>
					 </li>
					 <li class='menu selected'>
						 <h3 style="">Group Settings</h3>
						 <ul>
							<li><?php echo Html::anchor('manage/group/settings', __('General')); ?></li>
						<!--	<li><?php echo Html::anchor('manage/group/settings', __('Chain Map')); ?></li> -->
						 </ul>
					 </li>
					 <!--
					 <li class='menu selected'>
						 <h3 style="">Group Data</h3>
						 <ul>
							<li><?php echo Html::anchor('manage/group/settings', __('Logs')); ?></li>
							<li><?php echo Html::anchor('manage/group/settings', __('Statistics')); ?></li>
						 </ul>
					 </li>
					 -->
				<?php endif; ?>

				 <?php if(simpleauth::instance()->isAdmin()): ?>
					 <li class='menu selected'>
						 <h3 style="">Admin</h3>
						 <ul>
							<li><?php echo Html::anchor('manage/admin/groups', __('Groups')); ?></li>
						 </ul>
					 </li>
				<?php endif; ?>				
					 <li class='menu selected'>
						 <h3 style="">Account</h3>
						 <ul>
							<li><?php echo Html::anchor('user/profile', __('Details')); ?></li>
							<li><?php echo Html::anchor('user/profile_edit', __('Edit account')); ?></li>
						 </ul>
					 </li>			 
				 </ul> 
     </div>
     <div id='right'>
    <?php
     // output messages
     if(Message::count() > 0) {
       echo '<div class="content">';
       echo Message::output();
       echo '</div>';
     }
     ?>
				<?php echo $content; ?>
     </div>
     <?php endif; ?>
</div>
   
<?php 
// echo '<div id="kohana-profiler">'.View::factory('profiler/stats').'</div>';
?>
</body>
</html>
