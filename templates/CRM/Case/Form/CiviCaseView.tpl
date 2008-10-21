{* CiviCase -  view case screen*}
<div class="form-item">
<fieldset><legend>Case Summary</legend>
    <table class="form-layout-compressed">
        <tr>
            <td class="font-size12pt bold">&nbsp;Client: {$displayName}&nbsp;</td>
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
  <legend><a href="#" onclick="hide('caseRole'); show('caseRole_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close section"/></a>Case Roles</legend>
    <table class="report">
        <tr>
            <td class="label">Case Coordinator</td><td><a href="" title="view contact record">Greenberg, Dave</a> <a href="" title="edit case role"><img src="{$config->resourceBase}i/edit.png"></a></td><td>(415) 244-1092</td><td><a href="" title="Send Email"><img src="{$config->resourceBase}i/EnvelopeIn.gif"></a></td>
        </tr>
        <tr>
            <td class="label">Addiction Counselor</td><td><a href="" title="view contact record">Smith, Jane</a> <a href="" title="edit case role"><img src="{$config->resourceBase}i/edit.png"></a></td><td>(408) 552-3912</td><td><a href="" title="Send Email"><img src="{$config->resourceBase}i/EnvelopeIn.gif"></a></td>
        </tr>
        <tr>
            <td class="label">Primary Care Physician</td><td>(not assigned) <a href="" title="edit case role"><img src="{$config->resourceBase}i/edit.png"></a></td><td></td><td></td>
        </tr>
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
        <td class="label" colspan="2"><label for="activity_category">{ts}Category{/ts}</label><br />
            {$form.category.html}
        </td>
        <td class="label"><label for="reporter">Reporter/Role</label><br />
            <select name="reporter" id="reporter" class="form-select">
            <option value="" selected="selected">- any reporter -</option>
            <option>Greenberg, David (Case Coordinator)</option>
            <option>Smith, Jane (Addiction Counselor)</option>
            </select>
        </td>
        <td class="label"><label for="status">{$form.status_id.label}</label><br />
            {$form.status_id.html}
        </td>
        <td style="vertical-align: bottom;"><input class="form-submit default" name="_qf_Basic_refresh" value="Search" type="submit" /></td>
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

  <form title="activity_pager" action="" method="post">
  <table class="selector">
    <tr class="columnheader"><th scope="col"><a href="" class="sort-none">Category</a></th><th scope="col"><a href="" class="sort-none">Type</a></th><th scope="col"><a href="" class="sort-none">Reporter</a></th><th scope="col"><a href="" class="sort-none">Due</a></th><th scope="col"><a href="" class="sort-none">Completed</a></th><th scope="col"><a href="" class="sort-none">Status</a></th><th scope="col"></th></tr>
    <tr class="odd-row status-ontime"><td>Medical History</td><td><a href="">Complete Physical</a></td><td>(Primary Care Physician)</td><td>Oct 8th, 2008</td><td></td><td>Scheduled</td><td><a href="" >View</a>&nbsp;|&nbsp;<a href="" >Edit</a>&nbsp;|&nbsp;<a href="" >Delete</a></td></tr>
    <tr class="odd-row status-ontime"><td>Case History</td><td><a href="">Intake Assessment</a></td><td><a href="">Greenberg, Dave</a><br />(Case Coordinator)</td><td>Sept 24th, 2008</td><td>Sept 24th, 2008  5:00 PM</td><td>Completed</td><td><a href="" >View</a>&nbsp;|&nbsp;<a href="" >Edit</a>&nbsp;|&nbsp;<a href="" >Delete</a></td></tr>
  </table>
  </form>
</fieldset>
</div> <!-- End Activities div -->

<script type="text/javascript">
show('activities_show');
hide('activities');
</script>

</div>
