{* CiviCase -  view case screen*}
<div class="form-item">
<fieldset><legend>{ts}Case Summary{/ts}</legend>
    <table class="form-layout-compressed">
        <tr>
            <td class="font-size12pt bold">&nbsp;{ts}Client{/ts}: {$displayName}&nbsp;</td>
            <td class="right"><label>{$form.activity_id.label}</label>&nbsp;{$form.activity_id.html}<input type="button" accesskey="N" value="Go" name="new_activity" onclick="window.location=''"/></td>
            <td class="right">&nbsp;&nbsp;<label>{$form.report_id.label}</label>&nbsp;{$form.report_id.html}&nbsp;<input type="button" accesskey="R" value="Go" name="case_report" onclick="window.location=''"/></td> 
        </tr>
        <tr>
            <td style="border: solid 1px #dddddd; padding-right: 2em;"><label>{ts}Case Type:{/ts}</label>&nbsp;{$caseDetails.case_type}&nbsp;<a href="" title="Change case type (creates activity record)"><img src="{$config->resourceBase}i/edit.png" border="0"></a></td>
      	    <td style="border: solid 1px #dddddd; padding-right: 2em;"><label>{ts}Subject:{/ts}</label>&nbsp;{$caseDetails.case_subject}&nbsp;<a href="" title="Change case description (creates activity record)"><img src="{$config->resourceBase}i/edit.png" border="0"></a></td>
            <td style="border: solid 1px #dddddd; padding-right: 2em; vertical-align: bottom;"><label>{ts}Status:{/ts}</label>&nbsp;{$caseDetails.case_type}&nbsp;<a href="" title="Change case status (creates activity record)"><img src="{$config->resourceBase}i/edit.png" border="0"></a></td>
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
        {foreach from=$caseRelationships item=row}
        <tr>
            <td class="label">{$row.relation}</td><td><a href="{crmURL p='civicrm/contact/view' q="action=view&reset=1&cid=`$row.cid`"}" title="view contact record">{$row.name}</a> <a href="" title="edit case role"><img src="{$config->resourceBase}i/edit.png"></a></td><td>{$row.phone}</td><td>{if $row.email}<a href="{crmURL p='civicrm/contact/view/activity' q="atype=3&action=add&reset=1&cid=`$row.cid`"}"><img src="{$config->resourceBase}i/EnvelopeIn.gif" alt="{ts}Send Email{/ts}"/></a>&nbsp;{/if}</td>
        </tr>
        {/foreach}
        
        {foreach from=$caseRoles item=relName}
        <tr>
            <td class="label">{$relName}</td><td>(not assigned) <a href="" title="edit case role"><img src="{$config->resourceBase}i/edit.png"></a></td><td></td><td></td>
        </tr>
        {/foreach}
    </table>
 </fieldset>
</div>

<script type="text/javascript">
show('caseRole_show');
hide('caseRole');
</script>

<div id="activities_show" class="section-hidden section-hidden-border">
  <a href="#" onclick="hide('activities_show'); show('activities'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open section"/></a><label>Case Activities</label><br />
</div>

<div id="activities" class="section-shown">
<fieldset>
  <legend><a href="#" onclick="hide('activities'); show('activities_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close section"/></a>Case Activities</legend>
  <table class="no-border">
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
  <table id="activity" style="display:none"></table>

</fieldset>
</div> <!-- End Activities div -->


{literal}
<script type="text/javascript">
cj(document).ready(function(){

  var dataUrl = {/literal}"{crmURL p='civicrm/ajax/activity' h=0 q='snippet=4&caseID='}{$caseID}"{literal};

  cj("#activity").flexigrid
  (
    {
	url: dataUrl,
	    dataType: 'json',
	    colModel : [
			{display: 'Category', name : 'category', width : 100, sortable : true, align: 'left'},
			{display: 'Type', name : 'type', width : 100, sortable : true, align: 'left'},
			{display: 'Reporter', name : 'reporter', width : 100, sortable : true, align: 'left'},
			{display: 'Due', name : 'due_date', width : 100, sortable : true, align: 'left'},
			{display: 'Actual', name : 'actual_date', width : 100, sortable : true, align: 'left'},
			{display: 'Status', name : 'status', width : 90, sortable : true, align: 'left'},
			{display: '', name : 'links', width : 90, align: 'left'},
			],
	    sortname: "due_date",
	    sortorder: "desc",
	    usepager: true,
	    useRp: true,
	    rp: 10,
	    showTableToggleBtn: true,
            width: 815,
            height: 'auto',
            nowrap: false
	    }
   );   
  }
 );

function search(com)
{   
    /*
    var activity_date_low = cj("select#activity_date_low[M]").val() + '-' + cj("select#activity_date_low[d]").val() + '-' + cj("select#activity_date_low[Y]").val();
    */
    cj('#activity').flexOptions({
	    newp:1, 
		params:[{name:'category_0', value: cj("select#category_0").val()},
			{name:'category_1', value: cj("select#category_1").val()},
			{name:'reporter_id', value: cj("select#reporter_id").val()},
			{name:'status_id', value: cj("select#status_id").val()},
			{name:'date_range', value: cj("*[name=date_range]:checked").val()}
			]
		});
    
    cj("#activity").flexReload(); 
}

</script>
{/literal}

<script type="text/javascript">
show('activities_show');
hide('activities');
</script>
<br/>
{$form.buttons.html}
</div>
