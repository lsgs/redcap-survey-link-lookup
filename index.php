<?php
/**
 * Survey Link Lookup External Module
 * @author Luke Stevens, Murdoch Children's Research Institute
 */
include APP_PATH_DOCROOT . 'ControlCenter/header.php';

$module = new MCRI\SurveyLinkLookup\SurveyLinkLookup();
$module->printPage($_GET['lookup']);

include APP_PATH_DOCROOT . 'ControlCenter/footer.php';