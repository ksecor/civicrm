<div class="view-content">
{if $friends}
  {strip}
  <table class="selector">
    <tr class="columnheader">
      <th>&nbsp;</th>
      <th></th>
      <th></th>
    </tr>
     {counter start=0 skip=1 print=false}
     {foreach from=$friends item=row}
        <tr id='rowid{$row.contact_id}' class="{cycle values="odd-row,even-row"}">
           <td><img src="{$row.image}"></td>
           <td>
                <span class="label">{ts}Name{/ts}: </span><span>{$row.first_name}&nbsp;{$row.last_name}</span><br>
                <span class="label">{ts}Sex{/ts}: </span><span>{$row.sex}</span><br>
                <span class="label">{ts}Birthday{/ts}: </span><span>{$row.birthday}</span><br>
                <span class="label">{ts}Status{/ts}: </span><span>{$row.status.message}</span><br>
           </td>
           <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">View Contact in CiviCRM</a></td>
        </tr>
      {/foreach}
  </table>
  {/strip}
{else}
   <div class="messages status">
       <dl>
         <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
           <dd>
               {ts}You don't have any facebook friend(s) in CiviCRM.{/ts}
           </dd>
       </dl>
   </div>
{/if}
</div>
