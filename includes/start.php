<?php
/**
 * This does the initial setup for a web request.
 */

# Protect against register_globals
# This must be done before any globals are set by the code
if ( ini_get( 'register_globals' ) ) {
    if ( isset( $_REQUEST['GLOBALS'] ) || isset( $_FILES['GLOBALS'] ) ) {
        die( '<a href="http://www.hardened-php.net/globals-problem">$GLOBALS overwrite vulnerability</a>' );
    }
    $verboten = array(
        'GLOBALS',
        '_SERVER',
        'HTTP_SERVER_VARS',
        '_GET',
        'HTTP_GET_VARS',
        '_POST',
        'HTTP_POST_VARS',
        '_COOKIE',
        'HTTP_COOKIE_VARS',
        '_FILES',
        'HTTP_POST_FILES',
        '_ENV',
        'HTTP_ENV_VARS',
        '_REQUEST',
        '_SESSION',
        'HTTP_SESSION_VARS'
    );
    foreach ( $_REQUEST as $name => $value ) {
        if ( in_array( $name, $verboten ) ) {
            header( "HTTP/1.1 500 Internal Server Error" );
            echo "register_globals security paranoia: trying to overwrite superglobals, aborting.";
            die( -1 );
        }
        unset( $GLOBALS[$name] );
    }
}

$hdRequestTime = microtime( true );
unset( $IP );

# Valid web server entry point, enable includes.
define( 'HOTELDEALS', true );


# Full path to working directory.
$IP = getenv( 'HD_INSTALL_PATH' );
if ( $IP === false ) {
    if ( !isset( $hdUseAjax ) && realpath( '.' ) ) {
        $IP = realpath( '.' );
    } else {
        $IP = dirname( __DIR__ );
    }
}

/** Load global functions and settings file
 */
require_once "$IP/includes/GlobalFunctions.php";
require_once "$IP/includes/settings.php";
$GLOBALS['_settings'] = $_settings;                                 

# Start the autoloader, so that extensions can derive classes from core files
function loadModule( $className ) {                                                  
    global $IP;
    $className = ltrim( preg_replace( '/\\\\/', "/", $className ), '/' );              
                                                                                   
    if ( file_exists( $IP . '/includes/' . $className . '.php' ) )                         
        require_once( $IP . '/includes/' . $className .'.php' );                       
}                                                                                  
spl_autoload_register( 'loadModule' );


ini_set('date.timezone', 'Asia/Calcutta');                                      
date_default_timezone_set('Asia/Calcutta');

# Set error reporting options
error_reporting( E_ALL ^ E_STRICT );
if ( $_settings['debug'] ) {
    ini_set( 'display_erros', 1 );
} else {
    ini_set( 'display_erros', 0 ); # Production
}

# Load composer's autoloader if present
if ( is_readable( "$IP/vendor/autoload.php" ) ) {
    require_once "$IP/vendor/autoload.php";
}

/**
 * Set up global parameters for this application
 */
if ( isset( $_SERVER ) && isset( $_SERVER['REQUEST_URI'] ) ) {
    $serverRequestUri = $_SERVER['REQUEST_URI'];                           
                                                                                    
    $uriParsed = parse_url( $serverRequestUri );                                   
    $path = urldecode( $uriParsed['path'] );                                         
    $queryString = isSet( $uriParsed['query'] ) ? $uriParsed['query'] : '';


    $requestURI = explode( '/', $path );                                          
    $i = 0;                                                                     
    foreach ( $requestURI as $val ) {                                              
        if ( $val != '' )                                                          
            $segments[$i++] = $val;                                             
    }                                                                           
                                                                                
    //$hdRequestType = null;
    $hdRequestType = isset( $segments[0] ) ? $segments[0] : '';
    if ( !$hdRequestType ) {
        if ( isset( $_GET['type'] ) && $_GET['type'] ) {
            $hdRequestType = $_GET['type'];
            unset( $_GET['type'] );
        }
    }

    $params = array();
    parse_str( $queryString, $queryParams );
    foreach ( $queryParams as $param => $value ) {
        if ( Application::isAllowed( $param ) ) {
            $params[$param] = $value;
            unset( $_GET[$param] );
        }
    }
    $hdRequestParams = $params;
                                                                                
    $GLOBAL['hdRequestType'] = $hdRequestType;
    $GLOBAL['hdRequestParams'] = $hdRequestParams;
}
