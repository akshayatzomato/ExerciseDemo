<?php

/**
 * Main controller for this
 * application.
 */
class Application {
    

    /**
     * Allowed list of parameters
     * for api endpoints.
     */
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
    }

    /** 
     * Checks whether a particular query
     * parameter is valid within this context
     * or not.
     */
    public static function isAllowed( $param ) {
        return isset( self::$allowedParams[$param] ) ? true : false;
    }

    /**
     * Simple wrapper (sort of Factory) for
     * initiating an appropriate model.
     */
    public static function createDataObject() {
        global $hdRequestType, $hdRequestParams;
        $self = null;

        switch ( $hdRequestType ) {
            case 'deals':
                $self = new Deals( $hdRequestParams );
                return $self;
                break;
            default:
                return null; // throw Exception
                break;
        }
    }

    /**
     * Set up the OutputPage object
     * for handling output buffer.
     */
    private function setOutputObject() {
        $output = new OutputPage();    
        $output->mStatusCode = 200;
        $this->output = $output;
    }

    /**
     * Render HTML
     */
    private function render() {
        $this->setOutputObject();
        $this->output->setData( $this->data );
        $this->output->output();
    }

    /**
     * ~main() function
     */
    public function run() {
        $factory = self::createDataObject();
        $this->data = $factory->performRequest();
        $this->render();
    }
}
