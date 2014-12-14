<?php

class Template {

    private $out;
    private $data;
	/**
	 * Constructor
	 */
	function __construct( $out ) {
		$this->data = array();
        $this->out = $out; 
	}


    public function initTemplate() {
        $this->out->addStyle( 'deals.css', array() );
        $this->out->addScriptFile( 'jquery.min.js' );
        $this->out->addScriptFile( 'deals.js' );
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
		//$tpl->set( 'bottomscripts', $this->bottomScripts() );
        $tpl->set( 'headelement', $this->out->headElement( $this ) );
    }

	/**
	 * Sets the value $value to $name
	 * @param $name
	 * @param $value
	 */
	public function set( $name, $value ) {
		$this->data[$name] = $value;
	}

	/**
	 * Gets the template data requested
	 * @since 1.22
	 * @param string $name Key for the data
	 * @param mixed $default Optional default (or null)
	 * @return mixed The value of the data requested or the deafult
	 */
	public function get( $name, $default = null ) {
		if ( isset( $this->data[$name] ) ) {
			return $this->data[$name];
		} else {
			return $default;
		}
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function setRef( $name, &$value ) {
		$this->data[$name] =& $value;
	}

    public function html( $text ) {
        echo $text;
    }

	function printTrail() { ?>
        <?php $this->html( 'bottomscripts' );
	}
}
