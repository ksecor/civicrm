{* CiviCase -  view case screen*}
<div class="form-item">
<fieldset><legend>{ts}Case Summary{/ts}</legend>
    <table class="form-layout-compressed">
        <tr>
            <td class="font-size12pt bold">&nbsp;{ts}Client{/ts}: {$displayName}&nbsp;</td>
            <td class="right"><label>{ts}New Activity{/ts}</label>&nbsp;<input type="text" id="activity"/><input type="hidden" id="activity_id" value="">&nbsp;<input type="button" accesskey="N" value="Go" name="new_activity" onclick="window.location='{$newActivityUrl}' + document.getElementById('activity_id').value"/></td>
            <td class="right">&nbsp;&nbsp;<label>{$form.report_id.label}</label>&nbsp;{$form.report_id.html}&nbsp;<input type="button" accesskey="R" value="Go" name="case_report" onclick="window.location='{$reportUrl}' + document.getElementById('report_id').value"/></td> 
        </tr>
        <tr>
            <td style="border: solid 1px #dddddd; padding-right: 2em;"><label>{ts}Case Type:{/ts}</label>&nbsp;{$caseDetails.case_type}&nbsp;<a href="{crmURL p='civicrm/case/activity' q="action=add&reset=1&cid=`$contactId`&id=`$caseId`&selectedChild=activity&atype=`$changeCaseTypeId`"}" title="Change case type (creates activity record)"><img src="{$config->resourceBase}i/edit.png" border="0"></a></td>
            <td style="border: solid 1px #dddddd; padding-right: 2em; vertical-align: bottom;"><label>{ts}Status:{/ts}</label>&nbsp;{$caseDetails.case_status}&nbsp;<a href="{crmURL p='civicrm/case/activity' q="action=add&reset=1&cid=`$contactId`&id=`$caseId`&selectedChild=activity&atype=`$changeCaseStatusId`"}" title="Change case status (creates activity record)"><img src="{$config->resourceBase}i/edit.png" border="0"></a></td>
            <td class="right">&nbsp;&nbsp;<label>{$form.timeline_id.label}</label>&nbsp;{$form.timeline_id.html}&nbsp; {$form._qf_CaseView_next.html}</td> 
        </tr>
    </table>
</fieldset>
{literal}
<script type="text/javascript">
	
var activityUrl = {/literal}"{crmURL p='civicrm/ajax/activitytypelist' h=0 q='caseType='}{$caseDetails.case_type}"{literal};

cj("#activity").autocomplete( activityUrl, {
	width: 260,
	selectFirst: false  
});

cj("#activity").result(function(event, data, formatted) {
	cj("input[@id=activity_id]").val(data[1]);
});		    

</script>
{/literal}
<div id="caseRole_show" class="section-hidden section-hidden-border">
  <a href="#" onclick="hide('caseRole_show'); show('caseRole'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open section"/></a><label>Case Roles</label><br />
</div>

<div id="caseRole" class="section-shown">
 <fieldset>
  <legend><a href="#" onclick="hide('caseRole'); show('caseRole_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close section"/></a>{ts}Case Roles{/ts}</legend>
    <table class="report">
        {foreach from=$caseRelationships item=row key=relId}
        <tr>
            <td class="label">{$row.relation}</td><td><a id="relName_{$row.cid}" href="{crmURL p='civicrm/contact/view' q="action=view&reset=1&cid=`$row.cid`"}" title="view contact record">{$row.name}</a>&nbsp;<img src="{$config->resourceBase}i/edit.png" title="edit case role" onclick="createRelationship( {$row.relation_type}, {$row.cid}, {$relId} );"></td><td>{$row.phone}</td><td>{if $row.email}<a href="{crmURL p='civicrm/contact/view/activity' q="atype=3&action=add&reset=1&cid=`$row.cid`"}"><img src="{$config->resourceBase}i/EnvelopeIn.gif" alt="{ts}Send Email{/ts}"/></a>&nbsp;{/if}</td>
        </tr>
        {/foreach}
        
        {foreach from=$caseRoles item=relName key=relTypeID}
        <tr>
            <td class="label">{$relName}</td><td>(not assigned)&nbsp;<img title="edit case role" src="{$config->resourceBase}i/edit.png" onclick="createRelationship( {$relTypeID}, null, null );"></td><td></td><td></td>
        </tr>
        {/foreach}
    </table>
 </fieldset>
</div>
<div id="dialog">
     {ts}Begin typing to select contact.{/ts}<br/>
     <input type="text" id="rel_contact"/>
     <input type="hidden" id="rel_contact_id" value="">
</div>

{literal}
<script type="text/javascript">
show('caseRole_show');
hide('caseRole');

cj("#dialog").hide( );
function createRelationship( relType, contactID, relID ) {
    cj("#dialog").show( );

    cj("#dialog").dialog({
        title: "Assign Case Role",
	    modal: true, 
	    overlay: { 
		       opacity: 0.5, 
		        background: "black" 
		    },

	    open:function() {
		cj(this).parents(".ui-dialog:first").find(".ui-dialog-titlebar-close").remove();
		
		/* set defaults if editing */
		cj("#rel_contact").val( "" );
		cj("#rel_contact_id").val( null );
		if ( contactID ) {
		    cj("#rel_contact_id").val( contactID );
		    cj("#rel_contact").val( cj("#relName_" + contactID).text( ) );
		}
		
		var contactUrl = {/literal}"{crmURL p='civicrm/ajax/contactlist' h=0 }"{literal};

		cj("#rel_contact").autocomplete( contactUrl, {
			width: 260,
			selectFirst: false 
                 });

		cj("#rel_contact").result(function(event, data, formatted) {
			cj("input[@id=rel_contact_id]").val(data[1]);
		});		    
	    },
	    
	    buttons: { 
		"Ok": function() { 	    
		    if ( ! cj("#rel_contact").val( ) ) {
			alert('Select valid contact from the list.');
			return false;
		    }

		    var sourceContact = {/literal}"{$contactID}"{literal}
		    var caseID        = {/literal}"{$caseID}"{literal}

		    var v1 = cj("#rel_contact_id").val( );

		    if ( ! v1 ) {
			alert('Select valid contact from the list.');
			return false;
		    }

		    var postUrl = {/literal}"{crmURL p='civicrm/ajax/relation' h=0 }"{literal};
		    cj.post( postUrl, { rel_contact: v1, rel_type: relType, contact_id: sourceContact, rel_id: relID, case_id: caseID } );
		    
		    alert("Relationship record has been updated.");

		    cj(this).dialog("close"); 
		    cj(this).dialog("destroy"); 
		    
		    window.location.reload();
		},

		"Cancel": function() { 
		    cj(this).dialog("close"); 
		    cj(this).dialog("destroy"); 
		} 
	    } 

     });
}

cj(document).ready(function(){
   cj("#searchOptions").hide( );
});

function showHideSearch( ) {
    cj("#searchOptions").toggle( );
}

cj(document).ready(function(){
   cj("#view-activity").hide( );
});
function viewActivity( activityId ) {
    cj("#view-activity").show( );

    cj("#view-activity").dialog({
        title: "View Activity",
	    modal: true, 
	    width : 700,
        height : 650,
        resizable: true, 
	    overlay: { 
		       opacity: 0.5, 
		       background: "black" 
		    },
	    open:function() {
		cj(this).parents(".ui-dialog:first").find(".ui-dialog-titlebar-close").remove();
		cj("#activity-content").html("");
		var cid= {/literal}"{$contactID}"{literal};
        var viewUrl = {/literal}"{crmURL p='civicrm/case/activity/view' h=0 q="snippet=4" }"{literal};
		cj("#activity-content").load( viewUrl + "&cid="+cid + "&aid=" + activityId);
	    },
	    
	    buttons: { 
		"Done": function() { 	    
		    cj(this).dialog("close"); 
		    cj(this).dialog("destroy"); 
		}
	    } 
     });
}

</script>
{/literal}

<div id="activities_show" class="section-hidden section-hidden-border">
  <a href="#" onclick="hide('activities_show'); show('activities'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open section"/></a><label>{ts}Case Activities{/ts}</label><br />

<div id="view-activity">
     <div id="activity-content"></div>
</div>
</div>

<div id="activities" class="section-shown">
<fieldset>
  <legend><a href="#" onclick="hide('activities'); show('activities_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close section"/></a>{ts}Case Activities{/ts}</legend>
  <div><a id="searchFilter" href="javascript:showHideSearch( );">{ts}Search Filters{/ts}</a></div>
  <table class="no-border" id="searchOptions">
    <tr>
        <td class="label" colspan="2"><label for="activity_category">{ts}Category/Type{/ts}</label><br />
            {$form.category.html}
        </td>
        <td class="label"><label for="reporter">{ts}Reporter/Role{/ts}</label><br />
            {$form.reporter_id.html}
        </td>
        <td class="label"><label for="status">{$form.status_id.label}</label><br />
            {$form.status_id.html}
        </td>
	<td style="vertical-align: bottom;"><input class="form-submit default" name="_qf_Basic_refresh" value="Search" type="button" onclick="search()"; /></td>
    </tr>
    <tr>
        <td colspan="2"> 
	        {$form.date_range.html}
                 &nbsp;&nbsp; <label>- {ts}From{/ts}</label> 
                <br />
                {$form.activity_date_low.html}
                &nbsp;
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger_activity_1} 
                {include file="CRM/common/calendar/body.tpl" dateVar=activity_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_activity_1}
                
        </td>
        <td> 
                <label>{ts}To{/ts}</label><br />                  
                {$form.activity_date_high.html}
                &nbsp;
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger_activity_2} 
                {include file="CRM/common/calendar/body.tpl" dateVar=activity_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_activity_2}
        </td>
    </tr>
  </table>
  <br />
  <table id="activities-selector" style="display:none"></table>

</fieldset>
</div> <!-- End Activities div -->


{literal}
<script type="text/javascript">
cj(document).ready(function(){

  var dataUrl = {/literal}"{crmURL p='civicrm/ajax/activity' h=0 q='snippet=4&caseID='}{$caseID}"{literal};

  dataUrl = dataUrl + '&cid={/literal}{$contactID}{literal}';
  
  cj("#activities-selector").flexigrid
  (
    {
	url: dataUrl,
	    dataType: 'json',
	    colModel : [
			{display: 'Due', name : 'due_date', width : 100, sortable : true, align: 'left'},
			{display: 'Actual', name : 'actual_date', width : 100, sortable : true, align: 'left'},
                        {display: 'Subject', name : 'subject', width : 100, sortable : true, align: 'left'},
			{display: 'Category', name : 'category', width : 100, sortable : true, align: 'left'},
			{display: 'Type', name : 'type', width : 100, sortable : true, align: 'left'},
			{display: 'Reporter', name : 'reporter', width : 100, sortable : true, align: 'left'},
			{display: 'Status', name : 'status', width : 90, sortable : true, align: 'left'},
			{display: '', name : 'links', width : 90, align: 'left'},
			],
	    sortname: "due_date",
	    sortorder: "desc",
	    usepager: true,
	    useRp: true,
	    rp: 10,
	    showTableToggleBtn: true,
            width: 915,
            height: 'auto',
            nowrap: false
	    }
   );   
  }
 );

function search(com)
{   
    var month  = cj("select#activity_date_low\\[M\\]").val( );
    if ( month.length == 1 ) month = "0" + month;

    var day  = cj("select#activity_date_low\\[d\\]").val( );
    if ( day.length == 1 ) day = "0" + day;

    var activity_date_low  = cj("select#activity_date_low\\[Y\\]").val() + month + day;

    var month  = cj("select#activity_date_high\\[M\\]").val( );
    if ( month.length == 1 ) month = "0" + month;

    var day  = cj("select#activity_date_high\\[d\\]").val( );
    if ( day.length == 1 ) day = "0" + day;

    var activity_date_high  =  cj("select#activity_date_high\\[Y\\]").val() + month + day;

    cj('#activities-selector').flexOptions({
	    newp:1, 
		params:[{name:'category_0', value: cj("select#category_0").val()},
			{name:'category_1', value: cj("select#category_1").val()},
			{name:'reporter_id', value: cj("select#reporter_id").val()},
			{name:'status_id', value: cj("select#status_id").val()},
			{name:'date_range', value: cj("*[name=date_range]:checked").val()},
			{name:'activity_date_low', value: activity_date_low },
			{name:'activity_date_high', value: activity_date_high}
			]
		});
    
    cj("#activities-selector").flexReload(); 
}

function verifyActivitySet( ) {
    
    if ( document.getElementById('timeline_id').value == '' ) {
	alert("Please Select the Valid Activity Set.");
	return false;
    } 
    return confirm('Are you sure you want to add a set of scheduled activities to this case?');	 
}
</script>
{/literal}

{literal}
<script type="text/javascript">
{/literal}{if $show}{literal}
    hide('activities_show');
{/literal}{else}{literal}
    hide('activities');
{/literal}{/if}{literal}
</script>
{/literal}

{$form.buttons.html}
</div>
