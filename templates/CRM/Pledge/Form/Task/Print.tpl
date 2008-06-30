<p>

{if $rows } 
<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>
<div class="spacer"></div>
<br />
<p>
<table>
  <tr class="columnheader">
    <th>{ts}Signer{/ts}</th>
    <th>{ts}Pledge{/ts}</th>
    <th>{ts}Signed On{/ts}</th>
    <th>{ts}Anonymous?{/ts}</th>
  </tr>
{foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.sort_name}</td>
        <td>{$row.pledge}</td>  
        <td>{$row.pb_signer_signing_date|truncate:10:''|crmDate}</td> 
        <td>{if $row.pb_signer_is_anonymous}{ts}Yes{/ts}{else}{ts}No{/ts}{/if}</td>
    </tr>
{/foreach}
</table>

<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

{else}
   <div class="messages status">
    <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
    <dd>
        {ts}There are no records selected for Print.{/ts}
    </dd>
    </dl>
   </div>
{/if}
