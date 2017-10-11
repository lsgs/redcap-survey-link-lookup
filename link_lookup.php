<?php
/**
 * Survey Link Lookup External Module
 * @author Luke Stevens, Murdoch Children's Research Institute
 */
header("Content-Type: application/json");

$module = new SurveyLinkLookupExternalModule\SurveyLinkLookupExternalModule();

$lookupResult = array();

if (isset($_GET['lookup'])) {
        $lookupResult = $module->lookup($_GET['lookup']);
} else {
        $lookupResult['lookup_success'] = false;
        $lookupResult['lookup_result'] = 'No lookup value provided';
}
echo json_encode($lookupResult);