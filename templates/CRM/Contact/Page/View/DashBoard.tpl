<table class="no-border">
<tr>
<td>
    <fieldset><legend>{ts}Scheduled Activities{/ts}</legend>

    {if $rows}
        <form title="activity_pager" action="{crmURL}" method="post">
        {include file="CRM/common/pager.tpl" location="top"}
        {strip}
        <table>
          <tr class="columnheader">
          {foreach from=$columnHeaders item=header}
            <th>
            {if $header.sort}
              {assign var='key' value=$header.sort}
              {$sort->_response.$key.link}
            {else}
              {$header.name}
            {/if}
            </th>
          {/foreach}
          </tr>
        
         {counter start=0 skip=1 print=false}
         {foreach from=$rows item=row}  
            <tr class="{cycle values="odd-row,even-row"} {$row.class}">
                 <td>{$row.activity_type}</td>
                 <td>
                   <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=`$row.activity_type_id`&action=view&id=`$row.id`&cid=`$row.targetID`&history=0&context=Home"}">{$row.subject|mb_truncate:33:"...":true}</a>
                 </td>
                 <td>
                   <a href="{crmURL p='civicrm/contact/view/basic' q="reset=1&cid=`$row.sourceID`"}">{$row.sourceName}</a>
                 </td>
                 <td>
                   <a href="{crmURL p='civicrm/contact/view/basic' q="reset=1&cid=`$row.targetID`"}">{$row.targetName}</a>
                 </td>
                 <td>{$row.date|crmDate}</td>
                 <td>{$row.status_display}</td>
                 <td>{$row.action}</td>
               </tr>           
        {/foreach}
        </table>
        {/strip}
        {include file="CRM/common/pager.tpl" location="bottom"}
        </form>
    {else}
    <div>
        <strong>{ts}No activities are currently scheduled.{/ts}</strong>
    </div>
    {/if}
    </fieldset>
</td>
<td>
<fieldset><legend>{ts}Quick Search{/ts}</legend>
    <form action="{$postURL}" method="post">
    <div class="form-item">
        <input type="hidden" name="contact_type" value="" />
        <input type="text" name="sort_name" class="form-text required eight" value="" />
        <input type="submit" name="_qf_Search_refresh" value="{ts}Search{/ts}" class="form-submit" />
        <br />
        <a href="{$advancedSearchURL}" title="{ts}Go to Advanced Search{/ts}">&raquo; {ts}Advanced Search{/ts}</a>
    </div>
    </form>
</fieldset>
<fieldset><legend>{ts}Menu{/ts}</legend>
{$menuBlock.content}
</fieldset>
<fieldset><legend>{ts}Shortcuts{/ts}</legend>
{$shortcutBlock.content}
</fieldset>
</td>
</tr>
</table>
