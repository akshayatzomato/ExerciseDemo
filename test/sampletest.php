<?php
require_once __DIR__ . '/simpletest/autorun.php';
require_once __DIR__ . '/simpletest/web_tester.php';

class SimpleFormTests extends WebTestCase {
    function testPageExist() {
        $this->get('http://deals.expedia.com/beta/deals/hotels.json');
        $this->assertResponse(200);
    }
}

