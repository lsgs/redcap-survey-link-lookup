/**
 * Survey Link Looup External Module
 * @author Luke Stevens, Murdoch Children's Research Institute
 */
(function($, app_path_webroot_full, app_path_webroot, undefined) {

    function getResults(lookupVal) {
        return $.getJSON( 
            app_path_webroot_full+'modules/survey_link_lookup_v1.0/link_lookup.php', 
            { lookup: lookupVal },
            function(data) {
                return data;
            }
        )
        .fail(function() {
            return { 
                success: false,
                result: 'Lookup failed'
            };
        });
    }

    function searchBtnActiveState(active) {
        $('button#btnFind').prop("disabled",!active);
    }

    function resultPaneState(show) {
        var resultPane = $('div#results');
        var resultPaneDivs = $(resultPane).children('div');

        if (show) {
            $(resultPane).show();
            $(resultPaneDivs).each(function() {
                if (this.id===show) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $(resultPane).hide();
            $(resultPaneDivs).each(function() {
                $(this).hide();
            });
        }
    }

    function displayResults(results) {
        if (!results) {
            results.project_id = '';
            results.app_title = '';
            results.survey_title = '';
            results.app_title = '';
            results.record='';
            results.event_id='';
            results.form_name='';
            results.instance='';
        }
        var setupPageHref = (results.project_id) 
            ? app_path_webroot+'ProjectSetup/index.php?pid='+results.project_id
            : '#';
        var designPageHref = (results.project_id) 
            ? app_path_webroot+'Design/online_designer.php?pid='+results.project_id
            : '#';
        var dataEntryPageHref = (results.project_id && results.record) 
            ? app_path_webroot+'DataEntry/index.php?pid='+results.project_id+'&id='+results.record+'&event_id='+results.event_id+'&page='+results.form_name+'&instance='+results.instance
            : '#';

        $('span#result_project_id').html(results.project_id);
        $('span#result_app_title').html(results.app_title);
        $('span#result_survey_title').html(results.survey_title);
        $('span#result_record').html(results.record);
        $('span#result_event_name').html(results.event_name);
        $('span#result_instance').html(results.instance);
        $('a#result_link_setup_page').attr('href', setupPageHref);
        $('a#result_link_designer_page').attr('href', designPageHref);
        $('a#result_link_data_entry_page').attr('href', dataEntryPageHref);
    }

    function displayError(msg) {
        $('span#result_error_msg').html((msg)?msg:'');
    }

    function clearResults(){
        resultPaneState(false);
        displayResults(false);
        displayError(false);
    }

    function link_lookup() {
        clearResults();
        var searchFor = $('input#lookup_val').val();
        if (searchFor) {
            window.history.pushState({ dummy: true },"REDCap", app_path_webroot_full+"/modules/survey_link_lookup_v1.0/index.php?lookup="+searchFor);
            resultPaneState('results_spin');
            searchBtnActiveState(false);

            getResults(searchFor).then(function(results) {
                if (results.success) {
                    displayResults(results.result);
                    resultPaneState('results_detail');
                } else {
                    displayError(results.result);
                    resultPaneState('results_error');
                }
                searchBtnActiveState(true);
            });

        }
    }

    function init() {
        clearResults();
        $('button#btnFind').click(function() {
            link_lookup();
        });
    }

    $(document).ready(function() {
        init();
        // if page loads with a value in the search box, look it up
        link_lookup();
    });
})(jQuery, app_path_webroot_full, app_path_webroot);
