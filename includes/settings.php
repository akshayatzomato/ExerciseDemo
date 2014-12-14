<?php

define( 'HOST', 'https://fathomless-wildwood-9268.herokuapp.com/', false );
define( 'COOKIE_DOMAIN', 'herokuapp.com', false );
$_settings = array();

$_settings['debug'] = 1;

# Curl settings
$_settings['curl'] = array();
$_settings['curl']['CURLOPT_FRESH_CONNECT'] = true;
$_settings['curl']['CURLOPT_FOLLOWLOCATION'] = false;
$_settings['curl']['CURLOPT_FAILONERROR'] = true;
$_settings['curl']['CURLOPT_RETURNTRANSFER'] = true;
$_settings['curl']['CURLOPT_TIMEOUT'] = 10;
