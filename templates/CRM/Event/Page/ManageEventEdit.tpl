<h2>{$title}</h2>   


<table class="report"> 
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/event/manageEvent' q="reset=1&action=update&id=`$id`&subPage=EventInfo"}" id="idEventInformationandSettings">&raquo; {ts}Event Information and Settings{/ts}</a></td>
    <td>{ts}++.{/ts}</td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/event/manageEvent' q="reset=1&action=update&id=`$id`&subPage=Location"}" id="idLocation">&raquo; {ts}Location{/ts}</a></td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/event/manageEvent' q="reset=1&action=update&id=`$id`&subPage=Fee"}" id="idFee">&raquo; {ts}Fees{/ts}</a></td>
    <td>{ts}===={/ts}</td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/event/manageEvent' q="reset=1&action=update&id=`$id`&subPage=Registration"}" id="idRegistration">&raquo; {ts}Registration{/ts}</a></td>
    <td>{ts}----.{/ts}</td>
</tr>

</table>
