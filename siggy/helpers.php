<?php

function siggy_asset_url($asset, $secure = null): string
{
	$file = sha1($asset . SIGGY_VERSION);
	return URL::base(TRUE) . "assets/" . $file.".js";
}