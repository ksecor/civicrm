<h2>{$title}</h2>                                
<div class="messages status">
    <dl>
    {if $is_active}
        <dt><img src="{$config->resourceBase}i/traffic_green.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts}<p>This page is <strong>active</strong>.</p>
        <p>Link visitors to this page using the following URL:{/ts}<br />
        <a href="{crmURL p='civicrm/contribute/contribution' q="reset=1&id=`$id`"}">{crmURL p='civicrm/contribute/contribution' q="reset=1&id=`$id`"}</a>
        </dd>
    {else}
        <dt><img src="{$config->resourceBase}i/traffic_red.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts}<p>This page is currently <strong>inactive</strong> (not accessible to visitors).</p>
        <p>When you are ready to make this page live, click <a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Settings"}">Title and Settings</a>
        and update the <strong>Active?</strong> checkbox.</p>{/ts}</dd>
    {/if}
    </dl>
</div>

<div id="help">
    {ts}Use the options below to update features and content for this Online Contribution Page, as well as to
    run through the contribution process in <strong>test mode</strong>.{/ts}
</div>
<table class="report"> 
<tr>
    <td nowrap><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Settings"}">&raquo; {ts}Title and Settings{/ts}</a></td>
    <td>{ts}Set page title, contribution type (donation, campaign contribution, etc.), introduction, allowable payment types, activate the page.{/ts}</td>
</tr>
<tr>
    <td nowrap><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Amount"}">&raquo; {ts}Contribution Amounts{/ts}</a></td>
    <td>{ts}Configure contribution amount options and labels, minimum and maximum amounts.{/ts}</td>
</tr>
<tr>
    <td nowrap><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=ThankYou"}">&raquo; {ts}Thank-you and Receipting{/ts}</a></td>
    <td nowrap>{ts}Edit Thank-you page contents and receipting features.{/ts}</td>
</tr>
<tr>
    <td nowrap><a href="{crmURL p='civicrm/admin/contribute' q="reset=1&action=update&id=`$id`&subPage=Custom"}">&raquo; {ts}Custom Page Elements{/ts}</a></td>
    <td>{ts}Collect additional information from contributors by selecting CiviCRM Profile(s)
    to include in this contribution page.{/ts}</td>
</tr>
<tr>
    <td nowrap><a href="{crmURL p='civicrm/contribute/contribution' q="reset&action=preview&id=`$id`"}">&raquo; {ts}Test-drive{/ts}</a></td>
    <td>{ts}Test-drive the entire contribution process - including custom fields, confirmation, thank-you page, and receipting.
    Transactions will be directed to your Payment Processors test servers - <strong>no live charges will occur</strong>.{/ts}</td>
</tr>
</table>
