<?php

return [
	'outputPath' => 'public/assets/',
	'closurePath' => 'public/js/build/closure-compiler-v20170218.jar',
	'assets' => [
		'siggy.js' => [
			'type' => 'js',
			'basePath' => 'public/js/',
			'publicPath' => 'js/',
			'virtualName' => 'siggy.js',
			'files' => [
				'misc.js',
				'siggy.js',
				'siggy.helpers.js',
				'siggy.static.js',
				'siggy.dialogs.js',
				'siggy.intel.dscan.js',
				'siggy.intel.poses.js',
				'siggy.intel.structures.js',
				'siggy.charactersettings.js',
				'siggy.notifications.js',
				'siggy.timer.js',
				'siggy.sigtable.js',
				'siggy.globalnotes.js',
				'siggy.map.connection.js',
				'siggy.hotkeyhelper.js',
				'siggy.map.js',
				'siggy.eve.js',
				'siggy.activity.siggy.js',
				'siggy.activity.scannedsystems.js',
				'siggy.activity.search.js',
				'siggy.activity.thera.js',
				'siggy.activity.notifications.js',
				'siggy.activity.astrolabe.js',
				'siggy.activity.chainmap.js',  
				'siggy.vulnerabilitytable.js',
				'siggy.dialog.sigcreatewormhole.js',
				'siggy.dialog.structurevulnerability.js',
			]
		],
		'thirdparty.js' => [
			'type' => 'js',
			'basePath' => 'public/js/',
			'publicPath' => 'js/',
			'virtualName' => 'thirdparty.js',
			'files' => [
				'translate.js',
				'jquery/jquery-1.12.4.js',
				'jquery/jquery-ui.1.11.4.min.js',
				'jquery/jquery.qtip.js',
				'jquery/jquery.blockUI.js',
				'jquery/jquery.color.js',
				'jquery/jquery.tablesorter.js',
				'jquery/jquery.flot.js',
				'jquery/jquery.ui.position.js',
				'jquery/jquery.contextMenu.js',
				'jquery/jquery.hotkeys.js',
				'jquery/jquery.simplePagination.js',
				'jquery/jquery.idle.js',
				'jquery/jquery.jsPlumb-1.6.4.js',
				'vendor/handlebars-v4.0.5.js',
				'vendor/handlebars.form-helpers.js',
				'vendor/handlebars.helpers.js',
				'dropdown.js',
				'tab.js',
				'typeahead.bundle.js',
				'vendor/moment.js',
				'vendor/validate.js'
			]
		],
	]

];
