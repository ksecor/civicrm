{if $rows}
{include file="CRM/common/pager.tpl" location="top"}
   <table enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
   <thead>  
     <tr class="columnheader">
      <th field="Name" dataType="String" scope="col">{ts}Name{/ts}</th>
{if $participantListingType eq 3}
      <th field="Email" dataType="String" scope="col">{ts}Email{/ts}</th>
{/if}
     </tr>
   </thead>
   <tbody>  
   {foreach from=$rows item=row}
     <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.name}</td>	
{if $participantListingType eq 3}
        <td>{$row.email}</td>
{/if}
     </tr>
   {/foreach}
   </tbody>
   </table>
{include file="CRM/common/pager.tpl" location="bottom"}
{/if}