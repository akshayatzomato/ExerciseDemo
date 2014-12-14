<?php

class Application {
    

    public static $allowedParams = array(
        'sort_by',
        'filter_by',
        'limit',
        'start',
        'end'
    );

	/**
	 * Extra query params, e.g sort_by
	 */
    private $params;

	/**
	 * Type of request
	 */
    private $type;
	/**
	 * Lightweight constructor for an anonymous user.
	 *
	 */
    public function __construct() {
        global $hdRequestType, $hdRequestParams;
        $this->type = $hdRequestType;
        $this->params = $hdRequestParams;
        $this->output = OutputPage::getInstance();
    }

    public static function isAllowed( $param ) {
        return isset( self::$allowedParams[$param] ) ? true : false;
    }

    public static function createDataObject() {
        $self = null;

        switch ( $this->type ) {
            case 'deals':
                $self = new Deals( $this->params );
                return $self;
                break;
            default:
                return null; // throw Exception
                break;
        }
    }

    private function setOutputObject() {
        $output = new OutputPage();    
        $output->mStatusCode = 200;
        $this->output = $output;
    }

    private function render() {
        $this->setOutputObject();
        $this->output->setData( $this->data );
        $this->output->output();
    }

    public function run() {
        $factory = self::createDataObject();
        $this->data = $factory->performRequest();
        $this->render();
        //$this->restInPeace();
    }
}
