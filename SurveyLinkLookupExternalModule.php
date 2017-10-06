<?php
namespace SurveyLinkLookupExternalModule;

use ExternalModules\AbstractExternalModule;
use ExternalModules\ExternalModules;
use REDCap;
use HtmlPage;
use RCView;
use Project;

class SurveyLinkLookupExternalModule extends AbstractExternalModule
{
	public function __construct()
	{
		parent::__construct();
        }
        
        public function printPage($link='') {
                global $lang;
                
		$page = new HtmlPage();
		$page->PrintHeaderExt();
                
                if (!SUPER_USER) {
                        displayMsg('You do not have permission to view this page', 'errorMsg','center','red','exclamation_frame.png', 600);
                        $page->PrintFooterExt();
                        exit;
                }

                $link = REDCap::escapeHtml($link);

                include APP_PATH_VIEWS . 'HomeTabs.php';
                ?>
                <style type="text/css">
                    #pagecontent { margin-top: 70px; }
                </style>
                <script type="text/javascript" src="<?php echo APP_PATH_WEBROOT_FULL.'modules/survey_link_lookup_v1.0/survey_link_lookup.js';?>">
                </script>
                <?php
                renderPageTitle('Survey Link Lookup');

                print RCView::div(
                        array('id'=>'lookup_form'),
                        RCView::div(
                                array(),
                                'Enter a REDCap survey link or hash value to find the corresponding project, record and data entry form.'
                        ).
                        RCView::form(
                            array('name'=>'form', 'onsubmit'=>'return false;'),
                                RCView::div(
                                        array('class'=>'form-group', 'style'=>'margin:10px 0 20px 0'),
                                        RCView::label(array('for'=>'hash'),'Survey link or hash').
                                        RCView::input(
                                                array('type'=>'text','class'=>'form-control', 'style'=>'display:inline;width:200px;margin:0 5px',
                                                    'id'=>'lookup_val','value'=>$link)
                                        ).
                                        RCView::button(
                                                array('id'=>'btnFind', 'class'=>'btn btn-primary','type'=>'button'),
                                                '<span class="glyphicon glyphicon-search"></span>&nbsp;Find&nbsp;'
                                        )
                                )
                        )
                );

                print RCView::div(
                        array(
                                'id'=>'results', 'class'=>'container well', 'style'=>'width:inherit; font-size:120%; display:none;'
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
                                                'Project'
                                        ).
                                        RCView::div(
                                                array('class'=>'col-sm-6 col-md-6 col-lg-6'),
                                                '<span id="result_app_title"></span> (id=<span id="result_project_id"]."></span>)'
                                        ).
                                        RCView::div(
                                                array('class'=>'col-sm-3 col-md-3 col-lg-3'),
                                                RCView::a(
                                                        array('class'=>'btn btn-xs btn-default', 'target'=>'_blank', 'style'=>'text-align:center;width:150px',
                                                            'id'=>'result_link_setup_page', 'href'=>'#'),
                                                        'Project Setup'.
                                                        RCView::img(array('src'=>APP_PATH_IMAGES.'chain_arrow.png', 'style'=>'margin-left:3px'))
                                                )
                                        )
                                ).
                                RCView::div(
                                        array('class'=>'row', 'style'=>'margin-top:20px;margin-bottom:20px;'),
                                        RCView::div(
                                                array('class'=>'col-sm-2 col-md-2 col-lg-2', 'style'=>'color:#888'),
                                                'Survey'
                                        ).
                                        RCView::div(
                                                array('class'=>'col-sm-6 col-md-6 col-lg-6'),
                                                '<span id="result_survey_title"></span>'
                                        ).
                                        RCView::div(
                                                array('class'=>'col-sm-3 col-md-3 col-lg-3'),
                                                RCView::a(
                                                        array('class'=>'btn btn-xs btn-default', 'target'=>'_blank', 'style'=>'text-align:center;width:150px',
                                                            'id'=>'result_link_designer_page', 'href'=>'#'),
                                                        'Online Designer'.
                                                        RCView::img(array('src'=>APP_PATH_IMAGES.'chain_arrow.png', 'style'=>'margin-left:3px'))
                                                )
                                        )
                                ).
                                RCView::div(
                                        array('class'=>'row'),
                                        RCView::div(
                                                array('class'=>'col-sm-2 col-md-3 col-lg-2', 'style'=>'color:#888'),
                                                'Record<br>Event<br>Instance'
                                        ).
                                        RCView::div(
                                                array('class'=>'col-sm-6 col-md-6 col-lg-6'),
                                                '<span id="result_record"></span><br><span id="result_event_name"></span><br><span id="result_instance"></span>'
                                        ).
                                        RCView::div(
                                                array('class'=>'col-sm-3 col-md-3 col-lg-3'),
                                                RCView::a(
                                                        array('class'=>'btn btn-xs btn-default', 'target'=>'_blank', 'style'=>'text-align:center;width:150px',
                                                            'id'=>'result_link_data_entry_page', 'href'=>'#'),
                                                        'Data Entry Form'.
                                                        RCView::img(array('src'=>APP_PATH_IMAGES.'chain_arrow.png', 'style'=>'margin-left:3px'))
                                                )
                                        )
                                )
                        )
                );

                $page->PrintFooterExt();
                exit;
        }
        
        public function lookup($lookup_val) {
                $resultArray = array(
                        'success' => false,
                        'result' => ''
                );
                
                if (!isset($lookup_val) || $lookup_val=='') {
                        $resultArray['result'] = 'No link or survey hash provided';
                } else {
                        $hash = $this->extractHash($lookup_val);
                        if (!isset($hash) || $hash=='') {
                                $resultArray['result'] = "Nothing looking like a survey hash value found in '$lookup_val'";
                        } else {
                                try {
                                        $details = $this->readSurveyDetailsFromHash($hash);
                                        if (count($details) > 0) {
                                                $resultArray['success'] = true;
                                                $resultArray['result'] = $details;
                                        } else {
                                            $resultArray['result'] = "Survey hash '$hash' not found";
                                        }
                                } catch (Exception $ex) {
                                        $resultArray['result'] = $ex->getMessage();
                                }
                        }                        
                }
                return $resultArray;
        }
        
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

        private function readSurveyDetailsFromHash($hash) {

                $details = array();

                if (isset($hash) && $hash!=='') {

                        $sql = "SELECT s.survey_id,s.project_id,pr.app_title,s.form_name,s.title as survey_title,p.participant_id,p.event_id,p.hash,r.response_id,r.record,r.instance,r.start_time,r.first_submit_time,r.completion_time,r.return_code,r.results_code ".
                            "FROM redcap_surveys s ".
                            "INNER JOIN redcap_projects pr ON s.project_id = pr.project_id ".
                            "INNER JOIN redcap_surveys_participants p ON s.survey_id = p.survey_id ".
                            "INNER JOIN redcap_surveys_response r ON p.participant_id = r.participant_id ".
                            "WHERE hash = '".db_real_escape_string($hash)."' LIMIT 1";

                        $result = db_query($sql);

                        $details = db_fetch_assoc($result);
                        db_free_result($result);
                        
                        // get event name (with arm ref, if multiple)
                        if (isset($details['project_id']) && intval($details['project_id']) > 0) {
                                $event_name = '';
                                $project = new Project($details['project_id']);
                                if (!$project->longitudinal) {
                                        $event_name = 'N/A';
                                } else if ($project->multiple_arms) {
                                        $event_name = $project->eventInfo[$details['event_id']]['name_ext'];
                                } else {
                                        $event_name = $project->eventInfo[$details['event_id']]['name'];
                                }
                                $details['event_name'] = $event_name;
                        }
                }

                return $details;
        }
        
        public function redcap_control_center() {
                ?>
                <div id='control-center-plugins' style='display:none;'>
                    <div style="clear: both;padding-bottom:6px;margin:0 -6px 3px;border-bottom:1px solid #ddd;"></div>
                    <b style="position:relative;">Plugins</b><br/>
                    <span style="position: relative; float: left; left: 4px;">
                        <!-- Plugins - add a line here for each super-user plugin-->
                        <span class="glyphicon glyphicon-search"></span>&nbsp; <a href="<?php echo APP_PATH_WEBROOT_FULL;?>modules/survey_link_lookup_v1.0/index.php">Find Survey from Survey Hash</a><br/>
                    </span>
                </div>
                <script type='text/javascript'>
                $(document).ready(function() {
                    alert('todo: insert em link into menu');//$('#control-center-plugins').detach().insertAfter('#control_center_menu div:last').show();
                });
                </script>
                <?php
    
        }
}