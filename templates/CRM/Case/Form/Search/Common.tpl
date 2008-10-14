<div id="case-search" class="form-item">
<tr>
       	<td>
	        {$form.case_subject.label}<br />
	        {$form.case_subject.html}
        </td>
        <td>
            {$form.case_status_id.label}<br /> 
	        {$form.case_status_id.html}	
        </td>                    
    </tr>     
    <tr>
        <td>
            {$form.case_type_id.label}<br />
            {$form.case_type_id.html}          
        </td>
        <td>
            {$form.case_relation_type_id.label}<br />
            {$form.case_relation_type_id.html}<br /><br />
            {$form.name.label}
 
           <div class ="tundra" dojoType="dojox.data.QueryReadStore" jsId="contactStore" doClientPaging="false">
           {literal}
           <script type="text/javascript">
            dojo.addOnLoad( function( ) {  setUrl( ); });
		     function setUrl( ) {
   		       var relType = document.getElementById('case_relation_type_id').value;
		       var widget  = dijit.byId('case_role');
		       if ( relType ) {
			        widget.setDisabled( false );
			        var dataUrl = {/literal}'{crmURL p="civicrm/ajax/search" h=0 q="case=1&rel="}'{literal} + relType;
			        var queryStore = new dojox.data.QueryReadStore({url: dataUrl, jsId: 'contactStore', doClientPaging: false } );
			        widget.store = queryStore;
		       } else {
			        widget.setDisabled( true );
		       }
	         }
		     </script>
           {/literal}
           {$form.name.html}
        </td>
    </tr>
    <tr>
        <td colspan=2> 
            {$form.case_scheduledActivity_type_id.label}&nbsp;
            {$form.case_scheduledActivity_type_id.html}<br /><br />

            {$form.case_scheduledActivity_start_date_low.label|replace:'-':'<br />'} 
            {$form.case_scheduledActivity_start_date_low.html}&nbsp;
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_case_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=case_scheduledActivity_start_date_low  offset=3 trigger=trigger_search_case_1}
             	 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            {$form.case_scheduledActivity_start_date_high.label}
  	        {$form.case_scheduledActivity_start_date_high.html} &nbsp;
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_case_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=case_scheduledActivity_start_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_case_2}
                      
        </td>
    </tr>
    <tr>
        <td colspan=2>
            {$form.case_completedActivity_type_id.label}&nbsp;
            {$form.case_completedActivity_type_id.html}<br /><br />

            {$form.case_completedActivity_start_date_low.label|replace:'-':'<br />'} 
            {$form.case_completedActivity_start_date_low.html}&nbsp;
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_case_3}
            {include file="CRM/common/calendar/body.tpl" dateVar=case_completedActivity_start_date_low  offset=3 trigger=trigger_search_case_3}
             	 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            {$form.case_completedActivity_start_date_high.label}
  	        {$form.case_completedActivity_start_date_high.html} &nbsp;&nbsp;
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_case_4}
            {include file="CRM/common/calendar/body.tpl" dateVar=case_completedActivity_start_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_case_4}
                    
        </td>          
    </tr>
 {if $caseGroupTree}
    <tr>
        <td colspan="2">
            {include file="CRM/Custom/Form/Search.tpl" groupTree=$caseGroupTree showHideLinks=false}
        </td>
    </tr>
 {/if}
</div>