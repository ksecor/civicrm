{if $context EQ "Event"}
<table class="report">
      <thead class="sticky">
       	   <th scope="col">{ts}Event{/ts}</th>
           <th scope="col">{ts}Type{/ts}</th>
           <th scope="col">{ts}Public{/ts}</th>
           <th scope="col">{ts}Date(s){/ts}</th>
      </thead>

      {foreach from=$usedBy.civicrm_event item=event key=id}
           <tr>
               <td><a href="{crmURL p="civicrm/admin/event" q="action=update&reset=1&subPage=Fee&id=`$id`"}">{$event.title}</a></td>
               <td>{$event.eventType}</td>
               <td>{if $event.isPublic}{ts}Yes{/ts}{else}{ts}No{/ts}{/if}</td>
               <td>{$event.startDate|crmDate}{if $event.endDate}&nbsp;to&nbsp;{$event.endDate|crmDate}{/if}</td>
           </tr>
      {/foreach}
</table>
{elseif $context EQ "Contribution"}
<table class="report">
      <thead class="sticky">
       	   <th scope="col">{ts}Contribution Page{/ts}</th>
           <th scope="col">{ts}Type{/ts}</th>
           <th scope="col">{ts}Date(s){/ts}</th>
      </thead>

      {foreach from=$usedBy.civicrm_contribution_page item=contributionPage key=id}
           <tr>
               <td><a href="{crmURL p="civicrm/admin/contribute" q="action=update&reset=1&subPage=Amount&id=`$id`"}">{$contributionPage.title}</a></td>
               <td>{$contributionPage.type}</td>
               <td>{$contributionPage.startDate|crmDate}{if $contributionPage.endDate}&nbsp;to&nbsp;{$contributionPage.endDate|crmDate}{/if}</td>
           </tr>
      {/foreach}
</table>
{/if}