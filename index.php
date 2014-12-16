<?php

/** 
 * This is the main web entry point of this application. 
 */

# Initialise common code.
require __DIR__ . '/includes/start.php';

$main = new Application();
$main->run();


