<?php

class ApplicationTest extends PHPUnit_Framework_TestCase {
    public function testAllowedParams() {
        $param1 = 'city';
        $param2 = 'random'; 

        $this->assertEquals( true, Application::isAllowed( $param1 ) );
        $this->assertEquals( false, Application::isAllowed( $param2 ) );
    } 

    public function testDataObject() {
        global $hdRequestType;
        $hdRequestType = 'deals'; 

        $this->assertInstanceOf( 'DealsTemplate', Application::createDataObject() );

        $hdRequestType = 'index';
        $this->assertNull( Application::createDataObject() );

    }

    public function testOutputObject() {
        $app = new Application();
        $app->setOutputObject();

        $this->assertInstanceOf( 'OutputPage', $app->output );
    }


}
