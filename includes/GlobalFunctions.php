<?php

/**
 * Append a query string to an existing URL, which may or may not already
 * have query string parameters already. If so, they will be combined.
 *
 * @param $url String
 * @param $query Mixed: string or associative array
 * @return string
 */
function hdAppendQuery( $url, $query ) {
	if ( $query != '' ) {
		if ( false === strpos( $url, '?' ) ) {
			$url .= '?';
		} else {
			$url .= '&';
		}
		$url .= $query;
	}
	return $url;
}

/**
 * Get a non-repeating list of days 
 * starting from today.
 * @return array
 */
function hdGetUpcomingDays() {
    $days = array();

    $today = date( 'l' );
    $days[] = $today;
    $i = 1;
    while ( true ) {
        $next = date( 'l', strtotime( '+' . $i . ' day') );
        if ( in_array( $next, $days ) ) break;
        $days[] = $next;
        $i++; 
    }

    return $days;
}

/**
 * @return bool
 */
function hdHttpOnlySafe() {
	global $hdHttpOnlyBlacklist;

	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
		foreach ( $hdHttpOnlyBlacklist as $regex ) {
			if ( preg_match( $regex, $_SERVER['HTTP_USER_AGENT'] ) ) {
				return false;
			}
		}
	}

	return true;
}

function hdGetPriceRanges( $deals ) {
    $prices = array();
    
    foreach ( $deals as $deal ) {
       $prices[] = $deal['totalRate']; 
    } 

    sort( $prices, SORT_NUMERIC );
    $minPrice = floor( $prices[0] );
    $maxPrice = ceil( $prices[count($prices) - 1] );
    echo $minPrice . ' : ' . $maxPrice;

    $range = intval( ($maxPrice - $minPrice ) / 4 );
    $ranges = array();
    for ( $i = 1; $i <= 4; $i++ ) {
        $m = $minPrice;
        $M = $minPrice + $range;
        $ranges[] = array( $m, $M );
        $minPrice = $M;
    }

    return $ranges;
}
