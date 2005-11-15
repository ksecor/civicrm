<h2>{$title}</h2>                                
<div id="help">
    {ts}Use the links below to update the features and content for this Online Contribution Page.{/ts}
</div>
<table class="report"> 
<tr>                                              
    <td><a href="{crmURL p='civicrm/contribute' q="reset=1&action=update&id=`$id`&subPage=Settings"}">{ts}Title and Settings{/ts}</a></td>
    <td>{ts}Set page title, contribution type (donation, campaign contribution, etc.), introduction, allowable payment types, activate the page.{/ts}</td>
</tr>
<tr>
    <td><a href="{crmURL p='civicrm/contribute' q="reset=1&action=update&id=`$id`&subPage=Amount"}">{ts}Contribution Amounts{/ts}</a></td>
    <td>{ts}Configure contribution amount options and labels, minimum and maximum amounts.{/ts}</td>
</tr>
<tr>
    <td><a href="{crmURL p='civicrm/contribute' q="reset=1&action=update&id=`$id`&subPage=ThankYou"}">{ts}Thank-you and Receipting{/ts}</a></td>
    <td>{ts}Edit Thank-you page contents and receipting features.{/ts}</td>
</tr>
<tr>
    <td><a href="{crmURL p='civicrm/contribute' q="reset=1&action=update&id=`$id`&subPage=Custom"}">{ts}Custom Page Elements{/ts}</a></td>
    <td>{ts}Collect additional information from contributors by selecting CiviCRM Profile(s)
    to include in this contribution page.{/ts}</td>
</tr>
</table>
<div class="messages status">
    <dl>
    {if $is_active}
        <dt><img src="{$config->resourceBase}i/traffic_green.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts}<p>This page is <strong>active</strong>.
        You can <a href="{crmURL p='civicrm/contribute' q="reset&action=live&id=`$id`"}">View</a> this page as
        it appears to contributors.<p/>
        To create links to this page, copy and paste the following URL:{/ts}<br />
        <strong>{crmURL p='civicrm/contribute' q="reset&action=live&id=`$id`"}</strong>
        </dd>
    {else}
        <dt><img src="{$config->resourceBase}i/traffic_red.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts}<p>This page is currently <strong>inactive</strong>.
        You can <a href="{crmURL p='civicrm/contribute' q="reset&action=preview&id=`$id`"}">Preview</a> page content and layout.
        <p>When you are ready to make this page live, click <a href="{crmURL p='civicrm/contribute' q="reset=1&action=update&id=`$id`&subPage=Settings"}">Title and Settings</a>
        and update the <strong>Active?</strong> checkbox.</p>{/ts}</dd>
    {/if}
    </dl>
</div>
