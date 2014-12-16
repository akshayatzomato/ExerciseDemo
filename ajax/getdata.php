<?php

/**
 * Ideally a sanity check should be present here
 * but since we are not maintaining any sessions,
 * its OKAY to proceed.
 */

$hdUseAjax = true;
require_once __DIR__ . '/../includes/start.php';
# Fetch basic parameters
$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';
$sortBy = isset( $_POST['sortby'] ) ? $_POST['sortby'] : '';

$hdRequestType = 'deals';
$hdRequestParams['rating'] = $filter;
$hdRequestParams['sort'] = $sortBy;
$application = new Application();
$application->run();
