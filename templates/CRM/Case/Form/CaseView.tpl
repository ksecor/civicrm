{* CiviCase -  view case screen*}
<div class="form-item">
<fieldset><legend>{ts}Case Summary{/ts}</legend>
    <table class="report">
        <tr>
            <td class="font-size12pt">
                <label>{ts}Client{/ts}:</label>&nbsp;{$displayName}
            </td>
            <td>
                <label>{ts}Case Type{/ts}:</label>&nbsp;{$caseDetails.case_type}&nbsp;<a href="{crmURL p='civicrm/case/activity' q="action=add&reset=1&cid=`$contactId`&caseid=`$caseId`&selectedChild=activity&atype=`$changeCaseTypeId`"}" title="Change case type (creates activity record)"><img src="{$config->resourceBase}i/edit.png" border="0"></a>
            </td>
            <td>
                <label>{ts}Status{/ts}:</label>&nbsp;{$caseDetails.case_status}&nbsp;<a href="{crmURL p='civicrm/case/activity' q="action=add&reset=1&cid=`$contactId`&caseid=`$caseId`&selectedChild=activity&atype=`$changeCaseStatusId`"}" title="Change case status (creates activity record)"><img src="{$config->resourceBase}i/edit.png" border="0"></a>
            </td>
        </tr>
    </table>
    <table class="form-layout">
        <tr>
            <td>{$form.activity_type_id.label}<br />{$form.activity_type_id.html}&nbsp;<input type="button" accesskey="N" value="Go" name="new_activity" onclick="checkSelection( this );"/></td>
            <td>{$form.timeline_id.label}<br />{$form.timeline_id.html}&nbsp;{$form._qf_CaseView_next.html}</td> 
            <td>{$form.report_id.label}<br />{$form.report_id.html}&nbsp;<input type="button" accesskey="R" value="Go" name="case_report" onclick="checkSelection( this );"/></td> 
        </tr>
    </table>
</fieldset>

<div id="caseRole_show" class="section-hidden section-hidden-border">
  <a href="#" onclick="hide('caseRole_show'); show('caseRole'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open section"/></a><label>Case Roles</label><br />
</div>

<div id="caseRole" class="section-shown">
 <fieldset>
  <legend><a href="#" onclick="hide('caseRole'); show('caseRole_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close section"/></a>{ts}Case Roles{/ts}</legend>
    <table class="report">
		{assign var=rowNumber value = 1}
        {foreach from=$caseRelationships item=row key=relId}
        <tr>
            <td class="label">{$row.relation}</td><td id="relName_{$rowNumber}"><a href="{crmURL p='civicrm/contact/view' q="action=view&reset=1&cid=`$row.cid`"}" title="view contact record">{$row.name}</a>&nbsp;<img src="{$config->resourceBase}i/edit.png" title="edit case role" onclick="createRelationship( {$row.relation_type}, {$row.cid}, {$relId}, {$rowNumber} );"></td><td id="phone_{$rowNumber}">{$row.phone}</td><td id="email_{$rowNumber}">{if $row.email}<a href="{crmURL p='civicrm/contact/view/activity' q="atype=3&action=add&reset=1&cid=`$row.cid`"}"><img src="{$config->resourceBase}i/EnvelopeIn.gif" alt="{ts}Send Email{/ts}"/></a>&nbsp;{/if}</td>
        </tr>
		{assign var=rowNumber value = `$rowNumber+1`}
        {/foreach}
        
        {foreach from=$caseRoles item=relName key=relTypeID}
        <tr>
            <td class="label">{$relName}</td><td id="relName_{$rowNumber}">(not assigned)&nbsp;<img title="edit case role" src="{$config->resourceBase}i/edit.png" onclick="createRelationship( {$relTypeID}, null, null, {$rowNumber} );"></td><td id="phone_{$rowNumber}"></td><td id="email_{$rowNumber}"></td>
        </tr>
		{assign var=rowNumber value = `$rowNumber+1`}
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
function createRelationship( relType, contactID, relID, rowNumber ) {
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
				cj("#rel_contact").val( cj("#relName_" + rowNumber).text( ) );
			}

			var contactUrl = {/literal}"{crmURL p='civicrm/ajax/contactlist' h=0 }"{literal};

			cj("#rel_contact").autocomplete( contactUrl, {
				width: 260,
				selectFirst: false 
			});
			
			cj("#rel_contact").focus();
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
				cj.post( postUrl, { rel_contact: v1, rel_type: relType, contact_id: sourceContact, rel_id: relID, case_id: caseID },
						function( data ) {
							var resourceBase   = {/literal}"{$config->resourceBase}"{literal};
							var contactViewUrl = {/literal}"{crmURL p='civicrm/contact/view' q='action=view&reset=1&cid=' h=0 }"{literal};	
							var html = '<a href=' + contactViewUrl + data.cid +' title="view contact record">' +  data.name +'</a>&nbsp;<img src="' +resourceBase+'i/edit.png" title="edit case role" onclick="createRelationship( ' + relType +','+ data.cid +', ' + data.rel_id +', ' + rowNumber +' );">';
							cj('#relName_' + rowNumber ).html( html );
							
							html = '';
							if ( data.phone ) {
								html = data.phone;
							}	
							cj('#phone_' + rowNumber ).html( html );
							
							html = '';
							if ( data.email ) {
								var activityUrl = {/literal}"{crmURL p='civicrm/contact/view/activity' q='atype=3&action=add&reset=1&cid=' h=0 }"{literal};
								html = '<a href=' + activityUrl + data.cid + '><img src="'+resourceBase+'i/EnvelopeIn.gif" alt="Send Email"/></a>&nbsp;';
							} 
							cj('#email_' + rowNumber ).html( html );
							
					   	}, 'json' 
					);

				cj(this).dialog("close"); 
				cj(this).dialog("destroy"); 

				//window.location.reload();
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
</script>
{/literal}

{*include activity view js file*}
{include file="CRM/common/activityView.tpl"}

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
  <table class="no-border form-layout-compressed" id="searchOptions">
    <tr>
        <td><label for="reporter_id">{ts}Reporter/Role{/ts}</label><br />
            {$form.reporter_id.html}
        </td>
        <td><label for="status_id">{$form.status_id.label}</label><br />
            {$form.status_id.html}
        </td>
	<td style="vertical-align: bottom;"><input class="form-submit default" name="_qf_Basic_refresh" value="Search" type="button" onclick="search()"; /></td>
    </tr>
    <tr>
        <td> 
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
	{if $form.activity_deleted}    
	<tr>
		<td>
			{$form.activity_deleted.html}    
			{$form.activity_deleted.label}
		</td>
	</tr>
	{/if}
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
			{display: 'Due',     name : 'due_date',    width : 70, sortable : true, align: 'left'},
			{display: 'Actual',  name : 'actual_date', width : 70, sortable : true, align: 'left'},
            {display: 'Subject', name : 'subject',     width : 100, sortable : true, align: 'left'},
			{display: 'Type',    name : 'type',        width : 85, sortable : true, align: 'left'},
			{display: 'Reporter',name : 'reporter',    width : 90, sortable : true, align: 'left'},
			{display: 'Status',  name : 'status',      width : 60,  sortable : true, align: 'left'},
			{display: '',        name : 'links',       width : 70,  align: 'left'},
			],
	    usepager: true,
	    useRp: true,
	    rp: 10,
	    showTableToggleBtn: true,
        width: 680,
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

	var activity_deleted = 0;
	if ( cj("#activity_deleted:checked").val() == 1 ) {
		activity_deleted = 1;
	}
    cj('#activities-selector').flexOptions({
	    newp:1, 
		params:[{name:'reporter_id', value: cj("select#reporter_id").val()},
			{name:'status_id', value: cj("select#status_id").val()},
			{name:'date_range', value: cj("*[name=date_range]:checked").val()},
			{name:'activity_date_low', value: activity_date_low },
			{name:'activity_date_high', value: activity_date_high},
			{name:'activity_deleted', value: activity_deleted }
			]
		});
    
    cj("#activities-selector").flexReload(); 
}

function checkSelection( field ) {
	var validationMessage = '';
	var validationField   = '';
	var successAction     = '';
	
	var fName = field.name;
	
	switch ( fName )  {
		case '_qf_CaseView_next' :
				validationMessage = 'Please select an activity set from the list.';
				validationField   = 'timeline_id';
				successAction     = "confirm('Are you sure you want to add a set of scheduled activities to this case?');";
				break;

		case 'new_activity' :
				validationMessage = 'Please select an activity type from the list.';
				validationField   = 'activity_type_id';
				successAction     = "window.location='{/literal}{$newActivityUrl}{literal}' + document.getElementById('activity_type_id').value";
				break;

		case 'case_report' :
				validationMessage = 'Please select a report from the list.';
				validationField   = 'report_id';
				successAction     = "window.location='{/literal}{$reportUrl}{literal}' + document.getElementById('report_id').value";
				break;
	}	

	if ( document.getElementById( validationField ).value == '' ) {
		alert( validationMessage );
		return false;
	} else {
		return eval( successAction );
	}
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
