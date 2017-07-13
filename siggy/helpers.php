<?php

function siggy_asset_url($asset, $secure = null): string
{
	$file = sha1($asset . SIGGY_VERSION);
	return url("assets/" . $file.".js");
}

function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags); 
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

if (! function_exists('siggysession')) {
    /**
     * Gets reference to siggysession singleton
     * @return SiggySession
     */
    function siggysession()
    {
        if (is_null($key)) {
            return app('siggysession');
        }
    }
}