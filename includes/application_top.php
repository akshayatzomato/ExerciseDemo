<?php

ob_start();

include 'settings.php'; # Global Settings
$GLOBALS['_settings'] = $_settings;                                 

error_reporting( E_ALL ^ E_STRICT );

if ( $_settings['debug'] ) {
	ini_set( 'display_erros', 1 );
} else {
	ini_set( 'display_erros', 0 ); # Production
}


// Globals to add snippets in HTML template from anywhere                       
$_headBottom = array(); // contents will be appended just before </head>        
$_bodyStart = array(); // contents will be appended just after <body>           
$_bodyBottom = array(); // contents will be appended just before </body>        
$_pageScripts = array(); //contents that will be parsed after all async scripts have loaded and executed.


/**
* autoload function:
* For loading core modules
*/                      
function loadModule($className) {                                                  
    $className = ltrim(preg_replace('/\\\\/', "/", $className), '/');              
                                                                                   
    if(file_exists(APP_ROOT.'modules/'.$className.'.php'))                         
        require_once(APP_ROOT.'modules/'.$className.'.php');                       
    else if(file_exists(APP_ROOT.'library/'.$className.'.php'))                    
        require_once(APP_ROOT.'library/'.$className.'.php');                       
}                                                                                  
spl_autoload_register('loadModule');

ob_end_clean();