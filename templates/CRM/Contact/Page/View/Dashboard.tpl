<table class="form-layout">
<tr><td>
<div id="openActivities_show" class="data-group">
    {if $totalCountOpenActivity}
        <a href="#" onclick="hide('openActivities_show'); show('openActivities'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Scheduled Activities{/ts}</label> ({$totalCountOpenActivity})<br />
    {else}
        <dl><dt>{ts}Scheduled Activities{/ts}</dt>
            <dd>{ts}No activities currently scheduled.{/ts}</dd>
        </dl>
    {/if}
</div>

<div id="openActivities">
{if $rows}
 <fieldset><legend><a href="#" onclick="hide('openActivities'); show('openActivities_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{if $openActivity.totalCount GT 3}{ts 1=$openActivity.totalCount}Open Activities (3 of %1){/ts}{else}{ts}Scheduled Activities{/ts}{/if}</legend>
 <form title="activity_pager" action="{crmURL}" method="post">

    {include file="CRM/pager.tpl" location="top"}

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
               <a href="{crmURL p='civicrm/contact/view/activity' q="activity_id=`$row.activity_type_id`&action=view&id=`$row.id`&cid=`$contactId`&history=0"}">{$row.subject|mb_truncate:33:"...":true}</a>
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

    {include file="CRM/pager.tpl" location="bottom"}
    </form>
    </fieldset>
{else}
    <div class="data-group">
    <dl><dt>{ts}Scheduled Activities{/ts}</dt>
        <dd>{ts}No activities currently scheduled.{/ts}</dd>
    </dl>
    </div>
{/if}
</div>
<fieldset>
{$searchBlock.content}
</fieldset>
</td>
<td>
<fieldset><legend>{ts}Menu{/ts}</legend>
{$menuBlock.content}
</fieldset>
<fieldset><legend>{ts}Shortcuts{/ts}</legend>
{$shortcutBlock.content}
</fieldset>
</td>
</tr>
</table>
 <script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
 </script>
