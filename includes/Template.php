<?php

class Template {

    private $out;
    private $data;
    private $_data;
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
        $output->addStyle( 'deals.css', array() );
        $output->addScriptFile( 'jquery.min.js' );
        $output->addScriptFile( 'deals.js' );
        //$this->set( 'headlinks', $this->out->getHeadLinks() );
        //$this->set( 'csslinks', $this->out->buildCssLinks() );
		//$this->set( 'title', $this->out->getPageTitle() );
		//$this->set( 'pagetitle', $this->out->getHTMLTitle() );
		//$this->set( 'displaytitle', $this->out->mPageLinkTitle );
		//$tpl->setRef( 'mimetype', $hdMimeType );
		//$tpl->setRef( 'jsmimetype', $hdJsMimeType );
		//$tpl->set( 'charset', 'UTF-8' );
		//$tpl->setRef( 'wgScript', $hdScript );
		//$tpl->setRef( 'stylepath', $hdStylePath );
		//$tpl->setRef( 'scriptpath', $hdScriptPath );
		//$tpl->setRef( 'serverurl', $hdServer );
		//$tpl->setRef( 'logopath', $hdLogo );
		//$tpl->setRef( 'sitename', $hdSitename );
		//$tpl->set( 'lang', 'en' );
		$this->set( 'bottomscripts', $this->bottomScripts() );
        $this->set( 'headelement', $this->out->headElement( $this ) );
    }

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
	 * @since 1.22
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

    public function html( $key ) {
        echo $this->_data[$key];
    }

	function printTrail() { ?>
        <?php $this->html( 'bottomscripts' );
	}
}
