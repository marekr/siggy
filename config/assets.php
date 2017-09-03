<?php

return [
	'outputPath' => 'public/assets/',
	'webpacks' => [
		[
			'config' => 'webpack.config.vendor.js',
			'output_filename' => 'vendor.js'
		],
		[
			'config' => 'webpack.config.js',
			'output_filename' => 'siggy.js'
		]
	]
];
