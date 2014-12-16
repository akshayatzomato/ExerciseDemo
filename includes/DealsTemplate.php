<?php

/** Specific template for rendering deals
 * on the page. 
 */
class DealsTemplate extends Template {
    /**
     * View 
     */
    private static $view = 'dealsnew.php';
    public function __construct( $out ) {
        parent::__construct( $out );
    }

    /** 
     * Start outputting actual content
     * to the browser
     */
    public function render() {
        global $IP;
        $this->html( 'headelement' );
        $data = $this->data;
        ob_start();                                                                    
        include "$IP/views/" . self::$view;                            
        $body =  ob_get_contents();                                                    
        ob_clean();
        echo $body;
        $this->printTrail();
        echo OutputPage::closeElement( 'body' );
        echo OutputPage::closeElement( 'html' );
    }
}
