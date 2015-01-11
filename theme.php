<?php

define('ROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);
//ALWAYS USE NUMERIC IDS FOR SECURITY

require_once ROOT . 'application/vendor/lessphp/lessc.inc.php';

$id = 0;
if( isset( $_GET['id'] ) )
{
    $id = intval($_GET['id']);
}



$themeFiles = array( 'global_variables.css',
                      'reset.css',
                      'siggy.css',
                      'input.css',
                      'siggy.map.css',
                      'jquery.qtip.css',
					  'jquery.ui.css',
					  'jquery.contextMenu.css',
                      'autocomplete.css',
                      'icons.css',
                      'buttons.css',
                      'dropdown.css',
                      'overwrite.css',
					  'glyphicons.css',
					  'nav.css',
					  'sidebar.css'
					);


$cssBuffer = '';

foreach( $themeFiles as $fileName )
{
    if( file_exists( ROOT . 'public/themes/' . $id . '/' . $fileName ) )
    {
        $cssBuffer .= file_get_contents(ROOT . 'public/themes/' . $id . '/' . $fileName);
    }
    else
    {
        $cssBuffer .= file_get_contents(ROOT . 'public/themes/0/' . $fileName);
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
