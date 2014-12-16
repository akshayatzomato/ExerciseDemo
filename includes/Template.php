<?php

class Template {

    /**
     * Reference to the singleton 
     * OutputPage object.
     */
    private $out;

    /**
     * Data passed on to this object.
     * The actual content of the child
     * template.
     */
    protected $data;

    /**
     * Internal data structure
     */
    protected $_data;

    /**
     * Constructor
     */
    function __construct( $out ) {
        $this->data = array();
        $this->_data = array();
        $this->out = $out;

        $this->initTemplate();
    }

    public function setData( $data ) {
        $this->data = $data;
    }

    public function initTemplate() {
        $output = $this->out;
        $output->addStyle( 'dealsnew.css', array() );
        $output->addScriptFile( 'jquery.js' );
        $output->addScriptFile( 'deals.js' );
        $this->set( 'bottomscripts', $this->bottomScripts() );
        $this->set( 'headelement', $this->out->headElement( $this ) );
    }

    /**
     * Add scripts at the bottom
     * of the body tag. Not used
     * right now.
     */
    public function bottomScripts() {
        return "";
    }
    /**
     * Sets the value $value to $name
     * @param $name
     * @param $value
     */
    public function set( $name, $value ) {
        $this->_data[$name] = $value;
    }

    /**
     * Gets the template data requested
     * @param string $name Key for the data
     * @param mixed $default Optional default (or null)
     * @return mixed The value of the data requested or the deafult
     */
    public function get( $name, $default = null ) {
        if ( isset( $this->_data[$name] ) ) {
            return $this->_data[$name];
        } else {
            return $default;
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function setRef( $name, &$value ) {
        $this->_data[$name] =& $value;
    }

    /**
     * Echo the value for the key
     */
    public function html( $key ) {
        echo $this->_data[$key];
    }

    /** 
     * Print script tags
     * at the end of body tag.
     */
    function printTrail() { ?>
        <?php $this->html( 'bottomscripts' );
    }
}
