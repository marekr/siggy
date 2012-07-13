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
   <div id="page">
      <div id="header"><h1>siggy</h1></div>
      <div id="navigation">
         <ul class="menu">

             <?php
             $session = Session::instance();

             if (simpleauth::instance()->logged_in())
             {
              //  echo '<li>'.Html::anchor('admin_user', 'User admin').'</li>';
								if( simpleauth::instance()->isGroupAdmin() ) 
								{
									echo '<li>'.Html::anchor('manage/group', 'Manage Group').'</li>';  
								}       
                echo '<li>'.Html::anchor('user/profile', 'Account Management').'</li>';
                echo '<li>'.Html::anchor('user/logout', 'Log out').'</li>';
             } else {
              //  echo '<li>'.Html::anchor('user/register', 'Register').'</li>';
              //  echo '<li>'.Html::anchor('user/login', 'Log in').'</li>';
             }
           ?>
         </ul>
      </div>
   <div id="content">
    <?php
     // output messages
     if(Message::count() > 0) {
       echo '<div class="block">';
       echo '<div class="content" style="padding: 10px 15px;">';
       echo Message::output();
       echo '</div></div>';
     }
     ?>
     <?php if( !simpleauth::instance()->logged_in()): ?>
     <?php echo $content; ?>
     <?php else: ?>
     
     <div class="block" style="float:left;width:200px;">
			 <div class="content">
					<?php if( $controllerName == 'group' ): ?>
				 <h3 style="font-weight: bold;">Manage Group</h3>
				 <ul style="font-size: 0.8em;">
					<li><?php echo Html::anchor('manage/group', __('Group Members')); ?></li>
					<li><?php echo Html::anchor('manage/group/subgroups', __('Subgroups')); ?></li>
					<li><?php echo Html::anchor('manage/group/settings', __('Settings')); ?></li>
				 </ul>
				 <?php elseif($controllerName == 'user'): ?>
				 <h3 style="font-weight: bold;">Account Management</h3>
				 <ul style="font-size: 0.8em;">
					<li><?php echo Html::anchor('user/profile_edit', __('Edit account')); ?></li>
				 </ul>
				 <?php endif; ?>
			 </div>
     </div>
     <div class="block" style="margin-left:220px;">
				<?php echo $content; ?>
     </div>
     <?php endif; ?>
   </div>
</div>
   
<?php 
// echo '<div id="kohana-profiler">'.View::factory('profiler/stats').'</div>';
?>
</body>
</html>
