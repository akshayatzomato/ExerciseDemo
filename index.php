<?php

include_once __DIR__ . '/includes/application_top.php';

# Setting up curl request
$url = 'http://deals.expedia.com/beta/deals/hotels.json';
$session = curl_init( $url );                                           
curl_setopt( $session, CURLOPT_RETURNTRANSFER, true );                            
$json = curl_exec( $session );                                                   
$hotel_deals = json_decode( $json, true );

# Deal with output
foreach ( $hotel_deals as $deal ) {
    echo $deal['hotelId'] . PHP_EOL;
}


