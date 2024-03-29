<?php

define('ROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);
//ALWAYS USE NUMERIC IDS FOR SECURITY

require_once ROOT . '../vendor/lessphp/lessc.inc.php';

$id = 1;
if( isset( $_GET['id'] ) )
{
    $id = intval($_GET['id']);
}



$themeFiles = array( 'global_variables.css',
                      'reset.css',
                      'base.css',
                      'input.css',
                      'jquery.qtip.css',
					  'jquery.ui.css',
					  'jquery.contextMenu.css',
                      'icons.css',
                      'buttons.css',
                      'dropdown.css',
                      'overwrite.css',
					  'nav.css',
					  'sidebar.css',
                      'pagination.css',
                      'autocomplete.css',
                      'siggy.css',
                      'siggy.map.css',
                      'please-wait.css',
					);


$cssBuffer = '';

foreach( $themeFiles as $fileName )
{
    if( file_exists( ROOT . 'themes/' . $id . '/' . $fileName ) )
    {
        $cssBuffer .= file_get_contents(ROOT . 'themes/' . $id . '/' . $fileName);
    }
    else
    {
        $cssBuffer .= file_get_contents(ROOT . 'themes/1/' . $fileName);
    }
}

$seconds_to_cache = 0;
$ts               = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";

header("Expires: $ts");
header("Pragma: cache");
header("Cache-Control: max-age=$seconds_to_cache");
header("Content-type: text/css");

$less = new lessc;
echo $less->compile( $cssBuffer );
