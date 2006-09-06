<h2>{$title}</h2>                                
<div class="messages status">
    <dl>
    {if $is_active}
        <dt><img src="{$config->resourceBase}i/traffic_green.gif" alt="{ts}status{/ts}"/></dt>
        <dd><p>{ts}This page is <strong>active</strong>.{/ts}</p>
        <p>{ts}Link visitors to this page using the following URL{/ts}:</p>
        <a href="{crmURL p='civicrm/contribute/transact' q="reset=1&id=`$id`"}">{crmURL p='civicrm/contribute/transact' q="reset=1&id=`$id`"}</a>
        </dd>
    {else}
        <dt><img src="{$config->resourceBase}i/traffic_red.gif" alt="{ts}status{/ts}"/></dt>
        <dd><p>{ts}This page is currently <strong>inactive</strong> (not accessible to visitors).{/ts}</p>
        {capture assign=crmURL}{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Settings"}{/capture}
        <p>{ts 1=$crmURL}When you are ready to make this page live, click <a href="%1">Title and Settings</a> and update the <strong>Active?</strong> checkbox.{/ts}</p></dd>
    {/if}
    </dl>
</div>

<div id="help">
    {ts 1="http://wiki.civicrm.org/confluence//x/8xk"}Use the links below to update features and content for this Online Contribution Page, as well as to run through the contribution process in <strong>test mode</strong>. Refer to the <a href="%1" target="_blank" title="CiviContribute Guide. Opens documentation in a new window.">CiviContribute Guide</a> for more information.{/ts}
</div>
<table class="report"> 
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Settings"}" id="idTitleAndSettings">&raquo; {ts}Title and Settings{/ts}</a></td>
    <td>{ts}Set page title, contribution type (donation, campaign contribution, etc.), goal amount, introduction, allowable payment types, activate the page.{/ts}</td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Amount"}" id="idContributionAmounts">&raquo; {ts}Contribution Amounts{/ts}</a></td>
    <td>{ts}Configure contribution amount options and labels, minimum and maximum amounts.{/ts}</td>
</tr>
{if $CiviMember}
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Membership"}" id="idMembershipSettings">&raquo; {ts}Membership Settings{/ts}</a></td>
    <td>{ts}Configure membership sign-up and renewal options.{/ts}</td>
</tr>
{/if}
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=ThankYou"}" id="idThank-youandReceipting">&raquo; {ts}Thank-you and Receipting{/ts}</a></td>
    <td>{ts}Edit thank-you page contents and receipting features.{/ts}</td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Custom"}" id="idCustomPageElements">&raquo; {ts}Custom Page Elements{/ts}</a></td>
    <td>{ts}Collect additional information from contributors by selecting CiviCRM Profile(s) to include in this contribution page.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Premium"}" id="idPremiums">&raquo; {ts}Premiums{/ts}</a></td>
    <td>{ts}Enable a Premiums section (incentives / thank-you gifts) for this page, and configure premiums offered to contributors.{/ts}</td>
</tr>

<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/contribute/transact' q="reset=1&action=preview&id=`$id`"}" id="idTest-drive">&raquo; {ts}Test-drive{/ts}</a></td>
    <td>{ts}Test-drive the entire contribution process - including custom fields, confirmation, thank-you page, and receipting. Transactions will be directed to your payment processor's test server. <strong>No live financial transactions will be submitted. However, a contact record will be created or updated and a contribution record will be saved to the database. Use obvious test contact names so you can review and delete these records as needed.</strong>{/ts}</td>
</tr>
</table>
