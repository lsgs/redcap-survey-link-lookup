<?php
/**
 * REDCap External Module: Survey Link Lookup 
 * Look up the project / survey / record / event / instance corresponding to 
 * an individual survey link or hash.
 * @author Luke Stevens, Murdoch Children's Research Institute
 * TODO
 * - Include Control Center header and footer
 */
namespace MCRI\SurveyLinkLookup;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;
use REDCap;
use HtmlPage;
use RCView;

/**
 * REDCap External Module: Survey Link Lookup 
 */
class SurveyLinkLookup extends AbstractExternalModule
{
	public function __construct()
	{
		parent::__construct();
        }
        
        /**
         * Print the module plugin page html content 
         * @global array $lang
         * @param string $link Optional value to be written to the plugin page
         * input text box and searched for on document ready.
         */
        public function printPage($link='') {
                global $lang;
                
                if (!SUPER_USER) {
                        displayMsg($lang['global_05'], 'errorMsg','center','red','exclamation_frame.png', 600);
                        return;
                }

                $moduleJS = $this->getUrl('survey_link_lookup.js');
                ?>
                <style type="text/css">
                    #pagecontent { margin-top: 70px; }
                </style>
                <script type="text/javascript" src="<?php echo $moduleJS;?>"></script>
                <?php

                $link = REDCap::escapeHtml($link);
                $instructionText = $this->getSystemSetting('page-instruction-text');
                $inputLabelText = $this->getSystemSetting('page-input-label');

                include APP_PATH_VIEWS . 'HomeTabs.php';
                
                renderPageTitle('Survey Link Lookup');

                print RCView::div(
                        array('id'=>'lookup_form'),
                        RCView::div(
                                array(),
                                $instructionText
                        ).
                        RCView::form(
                            array('name'=>'form', 'onsubmit'=>'return false;'),
                                RCView::div(
                                        array('class'=>'form-group', 'style'=>'margin:10px 0 20px 0'),
                                        RCView::label(array('for'=>'hash'),$inputLabelText).
                                        RCView::input(
                                                array('type'=>'text','class'=>'form-control', 'style'=>'display:inline;width:200px;margin:0 5px',
                                                    'id'=>'lookup_val','value'=>$link)
                                        ).
                                        RCView::button(
                                                array('id'=>'btnFind', 'class'=>'btn btn-primaryrc','type'=>'button'),
                                                '<span class="fa f-search"></span>&nbsp;'.$lang['control_center_439'].'&nbsp;'
                                        )
                                )
                        )
                );

                print RCView::div(
                        array(
                                'id'=>'results', 'class'=>'container well', 'style'=>'width:100%; font-size:120%; display:none;'
                        ),
                        RCView::div(
                                array('id'=>'results_spin', 'class'=>'row', 'style'=>'display:block;text-align:center;'),
                                RCView::img(array('src'=>'progress_circle.gif'))
                        ).
                        RCView::div(
                                array('id'=>'results_error', 'class'=>'row', 'style'=>'display:block;text-align:center;'),
                                RCView::span(array('id'=>'result_error_msg', 'class'=>'text-danger'), 'error')
                        ).
                        RCView::div(
                                array('id'=>'results_detail', 'style'=>'display:block;'),
                                RCView::div(
                                        array('class'=>'row'),
                                        RCView::div(
                                                array('class'=>'col-sm-2 col-md-2 col-lg-2', 'style'=>'color:#888'),
                                                $lang['global_65'] //Project
                                        ).
                                        RCView::div(
                                                array('class'=>'col-sm-6 col-md-6 col-lg-6'),
                                                '<span id="result_app_title"></span> <span class="text-muted">(pid=<span id="result_project_id"]."></span>)</span>'
                                        ).
                                        RCView::div(
                                                array('class'=>'col-sm-3 col-md-3 col-lg-3'),
                                                RCView::a(
                                                        array('class'=>'btn btn-xs btn-defaultrc', 'target'=>'_blank', 'style'=>'text-align:center;min-width:12em;',
                                                            'id'=>'result_link_setup_page', 'href'=>'#'),
                                                        '<span class="fa fa-link"></span>&nbsp;'.$lang['app_17'].'&nbsp;<span class="fa fa-external-link-alt"></span>' //Project Setup
                                                )
                                        )
                                ).
                                RCView::div(
                                        array('class'=>'row', 'style'=>'margin-top:20px;margin-bottom:20px;'),
                                        RCView::div(
                                                array('class'=>'col-sm-2 col-md-2 col-lg-2', 'style'=>'color:#888'),
                                                $lang['survey_437'] //Survey'
                                        ).
                                        RCView::div(
                                                array('class'=>'col-sm-6 col-md-6 col-lg-6'),
                                                '<span id="result_survey_title"></span>'
                                        ).
                                        RCView::div(
                                                array('class'=>'col-sm-3 col-md-3 col-lg-3'),
                                                RCView::a(
                                                        array('class'=>'btn btn-xs btn-defaultrc', 'target'=>'_blank', 'style'=>'text-align:center;min-width:12em;',
                                                            'id'=>'result_link_designer_page', 'href'=>'#'),
                                                        '<span class="fa fa-link"></span>&nbsp;'.$lang['design_25'].'&nbsp;<span class="fa fa-external-link-alt"></span>' //Online Designer
                                                )
                                        )
                                ).
                                RCView::div(
                                        array('class'=>'row'),
                                        RCView::div(
                                                array('class'=>'col-sm-2 col-md-2 col-lg-2', 'style'=>'color:#888'),
                                                $lang['global_49'].'<br>'.$lang['global_141'].'<br>'.$lang['data_entry_246'] //Record<br>Event<br>Instance
                                        ).
                                        RCView::div(
                                                array('class'=>'col-sm-6 col-md-6 col-lg-6'),
                                                '<span id="result_record"></span><br><span id="result_event_name"></span><br><span id="result_instance"></span>'
                                        ).
                                        RCView::div(
                                                array('class'=>'col-sm-3 col-md-3 col-lg-3'),
                                                RCView::a(
                                                        array('class'=>'btn btn-xs btn-defaultrc', 'target'=>'_blank', 'style'=>'text-align:center;min-width:12em;',
                                                            'id'=>'result_link_data_entry_page', 'href'=>'#'),
                                                        '<span class="fa fa-link"></span>&nbsp;'.$lang['global_35'].'&nbsp;<span class="fa fa-external-link-alt"></span>' //Data Collection Instrument
                                                ).
                                                RCView::a(
                                                        array('class'=>'btn btn-xs btn-defaultrc', 'target'=>'_blank', 'style'=>'text-align:center;min-width:12em;display:none;',
                                                            'id'=>'result_link_public_survey_page', 'href'=>'#'),
                                                        '<span class="fa fa-link"></span>&nbsp;'.$lang['app_22'].'&nbsp;<span class="fa fa-external-link-alt"></span>' //Manage Survey Participants (Public Survey Link)
                                                )
                                        )
                                )
                        )
                );
                return;
        }
        
        /**
         * Extract the section of the input string that looks like a survey hash
         * @param string $lookup_val The string from which the survey hash value 
         * will be extracted.
         * @return array Array with two elements: 1) lookup_success (bool), 
         * indicating whether a valid hash was found in $lookup_val; 
         * 2) lookup_result (mixed), array of survey details or error message 
         */
        public function lookup($lookup_val) {
                $resultArray = array(
                        'lookup_success' => false,
                        'lookup_result' => ''
                );
                
                if (!isset($lookup_val) || $lookup_val=='') {
                        $resultArray['lookup_result'] = 'No link or survey hash provided';
                } else {
                        $hash = $this->extractHash($lookup_val);
                        if (!isset($hash) || $hash=='') {
                                $resultArray['lookup_result'] = "Could not extract survey hash value (?s=hash) from '$lookup_val'";
                        } else {
                                try {
                                        $details = $this->readSurveyDetailsFromHash($hash);
                                        if (count($details) > 0) {
                                                $resultArray['lookup_success'] = true;
                                                $resultArray['lookup_result'] = $details;
                                        } else {
                                            $resultArray['lookup_result'] = "Survey hash '$hash' not found";
                                        }
                                } catch (Exception $ex) {
                                        $resultArray['lookup_result'] = $ex->getMessage();
                                }
                        }                        
                }
                return $resultArray;
        }
        
        /**
         * Extract the section of the input string that looks like a survey hash
         * @param string $lookup_val The string from which the survey hash value 
         * will be extracted.
         * @return string Hash value (generally 10 characters), or empty string 
         * if no hash value found.
         */
        private function extractHash($lookup_val) {
            $hash = '';
            $matches = array();
            if (strpos($lookup_val, 's=')!==false) {
                    if (preg_match('/(?<=s=)[^\&]*/', $lookup_val, $matches)) {
                            $hashPart = $matches[0];
                    }
            } else {
                    $hashPart = $lookup_val;
            }
            if (preg_match('/^\w{6,10}$/', $hashPart, $matches)) {
                    $hash = $matches[0];
            }
            return $hash;
        }

        /**
         * Look up details of survey corresponding to the hash value provided
         * @param string $hash A (generally) 10-character value identifying an 
         * individual participant survey.
         * @return array
         */
        private function readSurveyDetailsFromHash($hash) {
                global $lang;
                
                $details = array();

                if (isset($hash) && $hash!=='') {

                        $sql = "SELECT s.survey_id,s.project_id,s.form_name,s.title as survey_title".
                                ",pr.app_title,pr.repeatforms".
                                ",p.participant_id,p.event_id,p.hash,IF(p.participant_email IS NULL,1,0) as is_public_survey_link".
                                ",em.descrip".
                                ",ea.arm_id,ea.arm_num,ea.arm_name".
                                ",proj_ea.num_project_arms".
                                ",r.response_id,r.record,r.instance,r.start_time,r.first_submit_time,r.completion_time,r.return_code,r.results_code ".
                            "FROM redcap_surveys s ".
                            "INNER JOIN redcap_projects pr ON s.project_id = pr.project_id ".
                            "INNER JOIN redcap_surveys_participants p ON s.survey_id = p.survey_id ".
                            "INNER JOIN redcap_events_metadata em ON em.event_id = p.event_id ".
                            "INNER JOIN redcap_events_arms ea ON ea.arm_id = em.arm_id ".
                            "INNER JOIN (SELECT project_id, COUNT(arm_id) as num_project_arms FROM redcap_events_arms GROUP BY project_id) proj_ea ON proj_ea.project_id = pr.project_id ".
                            "LEFT OUTER JOIN redcap_surveys_response r ON p.participant_id = r.participant_id ".
                            "WHERE hash = '".db_real_escape_string($hash)."' LIMIT 1";

                        $result = db_query($sql);

                        $details = db_fetch_assoc($result);
                        db_free_result($result);
                        
                        // get event name (with arm ref, if multiple)
                        if (isset($details['project_id']) && intval($details['project_id']) > 0) {
                                $event_name = '';
                                
                                if ($details['is_public_survey_link']) {
                                    $details['record'] = $lang['survey_279']; // Public Survey Link
                                    if (intval($details['num_project_arms']) > 1) { $event_name = $details['descrip']." (".$details['arm_name'].")"; }
                                    $details['instance'] = '';
                                    
                                } else if (!$details['repeatforms']) {
                                        $event_name = $lang['control_center_149']; // N/A (not a longitudinal project)
                                } else {
                                        $event_name = (intval($details['num_project_arms']) > 1)
                                                ? $event_name = $details['descrip']." (".$details['arm_name'].")"
                                                : $event_name = $details['descrip']; 
                                }
                                $details['event_name'] = $event_name;
                        }
                }

                return $details;
        }
}