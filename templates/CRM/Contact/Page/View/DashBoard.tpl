<table style="padding: 0px; border: 0px;">
<tbody style="padding: 0px; border: 0px;">
  <tr><td width="75%" style="padding: 0px; border: 0px;">  <!-- OPENING LEFT CELL -->



    <fieldset><legend>{ts}Scheduled Activities{/ts}</legend>

    {if $rows}
        <form title="activity_pager" action="{crmURL}" method="post">
        {include file="CRM/common/pager.tpl" location="top"}
        {strip}

<!--        <table border="0">
          <tr class="columnheader"><td colspan="6">
            Sort by: activity type | date scheduled
          </td></tr>
-->

<!--

          <tr class="columnheader">
          {foreach from=$columnHeaders item=header}
            <th scope="col">
            {if $header.sort}
              {assign var='key' value=$header.sort}
              {$sort->_response.$key.link}
            {else}
              {$header.name}
            {/if}
            </th>
          {/foreach}
          </tr>

-->
        
         {counter start=0 skip=1 print=false}
         {foreach from=$rows item=row}  
<!--            <tr class="{cycle values="odd-row,even-row"} {$row.class}"><td class="dashboard-cell">-->

		 <div class="dashboard-firstrow">
                  <span class="dashboard-date">{$row.date|crmDate:"%d/%m/%Y %H:%M"}</span>
                  <span class="dashboard-type">{$row.activity_type} ({$row.status_display}):</span>
     		  <a class="dashboard-subject" href="{crmURL p='civicrm/contact/view/activity' q="activity_id=`$row.activity_type_id`&action=view&selectedChild=activity&id=`$row.id`&cid=`$row.targetID`&history=0&context=Home"}">{$row.subject|mb_truncate:64:"...":true}</a>
                  <span class="dashboard-with">With: <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.targetID`"}">{$row.targetName}</a></span>
                  <span class="dashboard-by">added by: <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.sourceID`"}">{$row.sourceName}</a></span>
		 </div>
		 <div class="dashboard-third">

                  <div class="dashboard-actions">{$row.action}</div>
   		 </div>
                   
                 

<!--            </td></tr>           -->

        {/foreach}
<!--        </table> -->
        {/strip}
        {include file="CRM/common/pager.tpl" location="bottom"}
        </form>
    {else}
    <div>
        <strong>{ts}No activities are currently scheduled.{/ts}</strong>
    </div>
    {/if}


    </fieldset>


  </td> <!-- CLOSING LEFT CELL -->



  <td>  <!-- OPENING RIGHT CELL -->




<fieldset><legend>{ts}Quick Search{/ts}</legend>
    <form action="{$postURL}" method="post">
    <div class="form-item">
        {if $drupalFormToken}
            <input type="hidden" name="edit[token]" value="{$drupalFormToken}" />
        {/if}
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
{if $shortcutBlock.content}
    <fieldset><legend>{ts}Shortcuts{/ts}</legend>
    {$shortcutBlock.content}
    </fieldset>
{/if}



  </td></tr> <!-- CLOSING RIGHT CELL -->
</tbody>
</table>
