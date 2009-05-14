{if $rows}
<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

<div class="spacer"></div>

<div>
<br />
<table>
  <tr class="columnheader">
    <th>{ts}ID{/ts}</th>
    <th>{ts}Type{/ts}</th>
    <th>{ts}Name{/ts}</th>
    <th>{ts}Email{/ts}</th>
  </tr>

{foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.id}</td>
        <td>{$row.contact_type}</td>
        <td>{$row.name}</td>
        <td>{$row.email}</td>
    </tr>
{/foreach}
</table>
</div>

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
