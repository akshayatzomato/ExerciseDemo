<?php

class DealsTemplate extends Template {
    private static $view = 'deals.html';
    public function __construct() {
        $this->initTemplate();        
    }

    public function render() {
		$this->html( 'headelement' );
        ob_start();                                                                    
        include "$IP/views/" . self::$view;                            
        $body =  ob_get_contents();                                                    
        ob_clean();
		$this->printTrail();
		echo Html::closeElement( 'body' );
        echo Html::closeElement( 'html' );
    }

    public function getScripts() {
        return array(
            'deal.js'
        );
    }

    public function getStylesheets() {
        return array(
            'deal.css'
        );
    }
}
