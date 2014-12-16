<?php

class Deals {

	/**
	 * Constants
	 */
    private static $url = 'http://deals.expedia.com/beta/deals/hotels.json';

	/**
	 * Array with request params
	 */
    private $params;

	/**
	 * Array with loaded deals.
	 */
    private $deals;

    /**
    * Constructor function
    */
    public function __construct( $params ) {
        $this->deals = array();
        $this->params = $params;
    }

    /**
     * Create URL based on input params
     * @return string final url
     */
    private function getURL() {
        $url = self::$url;
        $params = array();
        foreach ( $this->params as $param => $value ) {
            if ( $param == 'rating' ) {
                if ( $value == 1 ) {
                    $params['minStarRating'] = 1;
                    $params['maxStarRating'] = 1.9;    
                } elseif ( $value == 2 ) {
                    $params['minStarRating'] = 2;
                    $params['maxStarRating'] = 2.9;
                } elseif ( $value == 3 ) {
                    $params['minStarRating'] = 3;
                    $params['maxStarRating'] = 3.9;
                } elseif ( $value == 4 ) {
                    $params['minStarRating'] = 4;
                    $params['maxStarRating'] = 5;

                }
            } else {
                $params[$param] = $value;
            }
        }
        $q = http_build_query( $params );
        $url .= ( $q ? ( '?' . $q ) : '' );

        return $url;
    }

	/**
	 * Fetch hotel deals via a CURL request
	 * @return array containing deals
	 */
    private function getDeals() {
        global $_settings, $hdRequestParams;
        $curl_options = $_settings['curl'];
        $sortBy = 'rating';

        $session = curl_init( $this->getURL() );                                           
        curl_setopt_array( $session, $curl_options );                            
        $json = curl_exec( $session );                                                   
        $hotel_deals = json_decode( $json, true );

        if ( isset( $hdRequestParams['sort'] ) && $hdRequestParams['sort'] == 'cost' ) {
            $sortBy = 'cost';
        }
        $hotel_deals = hdSortDeals( $hotel_deals, $sortBy );
        if ( $hotel_deals )
            $this->deals = $hotel_deals;
        return $hotel_deals;
    }

    /**
    * Override execute behaviour
    * Will vary depending on the type of 
    * functionality you want to override.
    */
    public function performRequest() {
        $this->getDeals();
        return $this->deals;
    }
}
