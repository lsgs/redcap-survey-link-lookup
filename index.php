<?php
/**
 * Survey Link Lookup External Module
 * @author Luke Stevens, Murdoch Children's Research Institute
 */
$module = new MCRI\SurveyLinkLookup\SurveyLinkLookup();
$module->printPage($_GET['lookup']);