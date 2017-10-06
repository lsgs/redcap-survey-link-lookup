<?php

/**
 * Survey Link Looup External Module
 * @author Luke Stevens, Murdoch Children's Research Institute
 */
error_reporting(0);
header("Content-Type: application/json");

require_once '../../redcap_connect.php';
require_once 'SurveyLinkLookupExternalModule.php';

$module = new SurveyLinkLookupExternalModule\SurveyLinkLookupExternalModule();

$lookupResult = array();

if (isset($_GET['lookup'])) {
        $lookupResult = $module->lookup($_GET['lookup']);
} else {
        $lookupResult['success'] = false;
        $lookupResult['result'] = 'No lookup value provided';
}
echo json_encode($lookupResult);