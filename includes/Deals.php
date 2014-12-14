<?php

class Deals {

	/**
	 * Constants
	 */
    private static DEALS_URL = 'http://deals.expedia.com/beta/deals/hotels.json';

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
        $url = self::DEALS_URL;

        $i = 0;
        foreach ( $this->params as $param => $value ) {
            if ( $i != 0 )
                $url .= '&';
            else
                $url .= '?';
            $url .= $param . '=' . $value;
            $i++;
        }

        return $url;
    }

	/**
	 * Fetch hotel deals via a CURL request
	 * @return array containing deals
	 */
    private function getDeals() {
        global $_settings;
        $curl_options = $_settings['curl'];

        $session = curl_init( $this->getURL() );                                           
        curl_setopt_array( $session, $curl_options );                            
        $json = curl_exec( $session );                                                   
        $hotel_deals = json_decode( $json, true );

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
