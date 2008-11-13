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
    <th>{ts}Name{/ts}</th>
    <th>{ts}Event{/ts}</th>
    <th>{ts}Fee Level{/ts}</th>
    <th>{ts}Fee Amount{/ts}</th>
    <th>{ts}Event Date{/ts}</th>
    <th>{ts}Status{/ts}</th>
    <th>{ts}Role{/ts}</th>
  </tr>
{foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.sort_name}</td>
	<td>{$row.event_title}</td>
            {assign var="participant_id" value=$row.participant_id}
        {if $lineItems.$participant_id}
        <td>
            {foreach from=$lineItems.$participant_id item=line name=lineItemsIter}
               {$line.label}: {$line.qty}
               {if ! $smarty.foreach.lineItemsIter.last}<br>{/if}
            {/foreach}
        </td>
        {else}
          <td>{if !$row.paid && !$row.participant_fee_level} {ts}(no fee){/ts}{else} {$row.participant_fee_level}{/if}</td>
          <td>{$row.participant_fee_amount|crmMoney}</td>
        {/if}
        <td>{$row.event_start_date|truncate:10:''|crmDate}
          {if $row.event_end_date && $row.event_end_date|date_format:"%Y%m%d" NEQ $row.event_start_date|date_format:"%Y%m%d"}
              <br/>- {$row.event_end_date|truncate:10:''|crmDate}
          {/if}
        </td>
        <td>{$row.participant_status_id}</td>
        <td>{$row.participant_role_id}</td>
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
