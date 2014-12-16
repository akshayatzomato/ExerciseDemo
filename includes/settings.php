<?php

define( 'HOST', 'https://fathomless-wildwood-9268.herokuapp.com/', false );
//define( 'HOST' , 'http://akshay.local/exercise/', false );

define( 'COOKIE_DOMAIN', 'herokuapp.com', false );

$_settings = array();
$_settings['debug'] = 1;

# Curl settings
$_settings['curl'] = array();
$_settings['curl'][CURLOPT_FRESH_CONNECT] = true;
$_settings['curl'][CURLOPT_FOLLOWLOCATION] = false;
$_settings['curl'][CURLOPT_FAILONERROR] = true;
$_settings['curl'][CURLOPT_RETURNTRANSFER] = true;
$_settings['curl'][CURLOPT_TIMEOUT] = 10;

# Global variables used
# throughout the app.
$hdUseAjax = false;
$hdMimeType = 'plain/html';

$hdJsMimeType = 'text/javascript';  

$hdStylePath = HOST . 'css/';

$hdScriptPath = HOST . 'javascript/'; 

$hdServer = HOST;  

$hdSitename = 'Hotel Deals'; 

$hdTitle = 'Hotel Deals'; 

$hdWellFormedXml = true;

/**
 * Default cookie expiration time. Setting to 0 makes all cookies session-only.
 */
$hdCookieExpiration = 180 * 86400;

/**
 * Set to set an explicit domain on the login cookies eg, "justthis.domain.org"
 * or ".any.subdomain.net"
 */
$hdCookieDomain = '';

/**
 * Set this variable if you want to restrict cookies to a certain path within
 * the domain specified by $wgCookieDomain.
 */
$hdCookiePath = '/';

/**
 * Whether the "secure" flag should be set on the cookie. This can be:
 *   - true:      Set secure flag
 *   - false:     Don't set secure flag
 *   - "detect":  Set the secure flag if $wgServer is set to an HTTPS URL
 */
$hdCookieSecure = 'detect';

/**
 * By default, MediaWiki checks if the client supports cookies during the
 * login process, so that it can display an informative error message if
 * cookies are disabled. Set this to true if you want to disable this cookie
 * check.
 */
$hdDisableCookieCheck = false;

/**
 * Cookies generated by MediaWiki have names starting with this prefix. Set it
 * to a string to use a custom prefix. Setting it to false causes the database
 * name to be used as a prefix.
 */
$hdCookiePrefix = false;

/**
 * Set authentication cookies to HttpOnly to prevent access by JavaScript,
 * in browsers that support this feature. This can mitigates some classes of
 * XSS attack.
 */
$hdCookieHttpOnly = true;

/**
 * If the requesting browser matches a regex in this blacklist, we won't
 * send it cookies with HttpOnly mode, even if $wgCookieHttpOnly is on.
 */
$hdHttpOnlyBlacklist = array(
    // Internet Explorer for Mac; sometimes the cookies work, sometimes
    // they don't. It's difficult to predict, as combinations of path
    // and expiration options affect its parsing.
    '/^Mozilla\/4\.0 \(compatible; MSIE \d+\.\d+; Mac_PowerPC\)/',
);
