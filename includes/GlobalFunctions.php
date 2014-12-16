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
    $date = date( 'Y-m-d' );
    $days[] = array( 'day' => $today, 'date' => $date );
    $i = 1;
    while ( true ) {
        if ( $i == 7 ) break;
        $next = date( 'l', strtotime( '+' . $i . ' day') );
        $nextdate = date( 'Y-m-d', strtotime( '+' . $i . ' day') );
        $days[] = array( 'day' => $next, 'date' => $nextdate );
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

function hdGetStars( $rating ) {
    $stars = intval( floor( $rating ) );
    return $stars;
}

function hdSortDeals( $deals, $sortBy = 'rating' ) {
    $oDeals = $deals; 
    $ratings = array();                                                             
    foreach ($oDeals as $key => $row) {                                          
        $ratings[$key] = ( $sortBy == 'rating' ? $row['starRating'] : $row['totalRate'] );                                             
    }                                                                              
    array_multisort($ratings, SORT_DESC, $oDeals);

    return $oDeals;
}
