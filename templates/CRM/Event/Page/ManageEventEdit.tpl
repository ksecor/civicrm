<h2>{$title}</h2>   


<table class="report"> 
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/event' q="reset=1&action=update&id=`$id`&subPage=EventInfo"}" id="idEventInformationandSettings">&raquo; {ts}Event Information and Settings{/ts}</a></td>
    <td>{ts}Set page title, event type (conference, concert etc.), Start date and time,End date and time,activate the page etc{/ts}</td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/event' q="reset=1&action=update&id=`$id`&subPage=Location"}" id="idLocation">&raquo; {ts}Location{/ts}</a></td>
    <td>{ts}Set the location fields.{/ts}</td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/event' q="reset=1&action=update&id=`$id`&subPage=Fee"}" id="idFee">&raquo; {ts}Fees{/ts}</a></td>
    <td>{ts}Set the paid event ,contribution type,configure contribution amount options and labels.{/ts}</td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/event' q="reset=1&action=update&id=`$id`&subPage=Registration"}" id="idRegistration">&raquo; {ts}Registration{/ts}</a></td>
    <td>{ts}Configure Online registration.{/ts}</td>
</tr>

</table>
