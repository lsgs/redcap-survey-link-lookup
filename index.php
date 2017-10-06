<?php
/**
 * Survey Link Looup External Module
 * @author Luke Stevens, Murdoch Children's Research Institute
 */

require_once '../../redcap_connect.php';
require_once 'SurveyLinkLookupExternalModule.php';

$module = new SurveyLinkLookupExternalModule\SurveyLinkLookupExternalModule();

$module->printPage($_GET['lookup']);