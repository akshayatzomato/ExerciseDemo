<?php
/**
 * Class used to send headers and cookies back to the user
 */
class WebResponse {

    /**
     * Output a HTTP header, wrapper for PHP's
     * header()
     * @param string $string header to output
     * @param bool $replace replace current similar header
     * @param $http_response_code null|int Forces the HTTP response code to the specified value.
     */
    public function header( $string, $replace = true, $http_response_code = null ) {
        header( $string, $replace, $http_response_code );
    }

    /**
     * Set the browser cookie
     * @param string $name name of cookie
     * @param string $value value to give cookie
     * @param int|null $expire Unix timestamp (in seconds) when the cookie should expire.
     */
    public function setcookie( $name, $value, $expire = 0, $options = null ) {
        global $hdCookiePath, $hdCookiePrefix, $hdCookieDomain;
        global $hdCookieSecure, $hdCookieExpiration, $hdCookieHttpOnly;

        if ( !is_array( $options ) ) {
            // Backwards compatability
            $options = array( 'prefix' => $options );
            if ( func_num_args() >= 5 ) {
                $options['domain'] = func_get_arg( 4 );
            }
            if ( func_num_args() >= 6 ) {
                $options['secure'] = func_get_arg( 5 );
            }
        }
        $options = array_filter( $options, function ( $a ) {
            return $a !== null;
        } ) + array(
            'prefix' => $hdCookiePrefix,
            'domain' => $hdCookieDomain,
            'path' => $hdCookiePath,
            'secure' => $hdCookieSecure,
            'httpOnly' => $hdCookieHttpOnly,
            'raw' => false,
        );

        if ( $expire === null ) {
            $expire = 0; // Session cookie
        } elseif ( $expire == 0 && $hdCookieExpiration != 0 ) {
            $expire = time() + $hdCookieExpiration;
        }

        // Don't mark the cookie as httpOnly if the requesting user-agent is
        // known to have trouble with httpOnly cookies.
        if ( !hdHttpOnlySafe() ) {
            $options['httpOnly'] = false;
        }

        $func = $options['raw'] ? 'setrawcookie' : 'setcookie';
        call_user_func( $func,
            $options['prefix'] . $name,
            $value,
            $expire,
            $options['path'],
            $options['domain'],
            $options['secure'],
            $options['httpOnly'] );
    }
}
