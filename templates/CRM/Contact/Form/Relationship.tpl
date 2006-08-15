{* this template is used for adding/editing/viewing relationships  *}

{if $action eq 4 } {* action = view *}
    <div class="form-item">
      <fieldset><legend>{ts}View Relationship{/ts}</legend>

        <div class="form-item">
	    {foreach from=$viewRelationship item="row"}
            <dl>
            <dt>{$row.relation}</dt> 
            <dd class="label"><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.cid`"}">{$row.name}</a></dd>
            {if $row.start_date}
                <dt>{ts}Start Date:{/ts}</dt><dd>{$row.start_date|crmDate}</dd>
            {/if}
            {if $row.end_date}
                <dt>{ts}End Date:{/ts}</dt><dd>{$row.end_date|crmDate}</dd>
            {/if}
            {if $row.description}
                <dt>{ts}Description:{/ts}</dt><dd>{$row.description}</dd>
            {/if}
	    {foreach from=$viewNote item="rec"}
		    {if $rec.note}
			<dt>{ts}Note:{/ts}</dt><dd>{$rec.note}</dd>	
	   	    {/if}
            {/foreach}
            <dt>{ts}Status:{/ts}</dt><dd>{if $row.is_active}{ts}Enabled{/ts} {else} {ts}Disabled{/ts}{/if}</dd>
            </dl>
	 	    {include file="CRM/Contact/Page/View/InlineCustomData.tpl" mainEditForm=1}
            <dl>
            <dt></dt>
            <dd><input type="button" name='cancel' value="{ts}Done{/ts}" onclick="location.href='{crmURL p='civicrm/contact/view/rel' q='action=browse'}';"/></dd>
            </dl>
            {/foreach}
		
        </div>
        </fieldset>
     </div>    
{/if}
 {if $action eq 2 | $action eq 1} {* add and update actions *}
    <fieldset><legend>{if $action eq 1}{ts}New Relationship{/ts}{else}{ts}Edit Relationship(s){/ts}{/if}</legend>
        <div class="form-item">
            {if $action eq 1}
                <div class="description">
                {ts}Select the relationship type. Then locate target contact(s) for this relationship by entering a complete or partial name and clicking 'Search'.{/ts}
                </div>
            {/if}
            <dl>
            <dt>{$form.relationship_type_id.label}</dt><dd>{$form.relationship_type_id.html}
            {if $action EQ 2} {* action = update *}
                <label>{$sort_name}</label></dd>
                </dl>
            {else} {* action = add *}
                </dd>
                <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
                <dt> </dt>
                  <dd>
                    {$form._qf_Relationship_refresh.html}
                    {$form._qf_Relationship_cancel.html}
                  </dd>
                </dl>

              {if $searchDone } {* Search button clicked *}
                {if $searchCount}
                    {if $searchRows} {* we've got rows to display *}
                        <fieldset><legend>{ts}Mark Target Contact(s) for this Relationship{/ts}</legend>
                        <div class="description">
                            {ts}Mark the target contact(s) for this relationship if it appears below. Otherwise you may modify the search name above and click Search again.{/ts}
                        </div>
                        {strip}
                        <table>
                        <tr class="columnheader">
                        <th>&nbsp;</th>
                        <th>{ts}Name{/ts}</th>
                        <th>{ts}City{/ts}</th>
                        <th>{ts}State{/ts}</th>
                        <th>{ts}Email{/ts}</th>
                        <th>{ts}Phone{/ts}</th>
                        </tr>
                        {foreach from=$searchRows item=row}
                        <tr class="{cycle values="odd-row,even-row"}">
                            <td>{$form.contact_check[$row.id].html}</td>
                            <td>{$row.type} {$row.name}</td>
                            <td>{$row.city}</td>
                            <td>{$row.state}</td>
                            <td>{$row.email}</td>
                            <td>{$row.phone}</td>
                        </tr>
                        {/foreach}
                        </table>
                        {/strip}
                        </fieldset>
                    {else} {* too many results - we're only displaying 50 *}
                        </div></fieldset>
                        {if $duplicateRelationship}  
                          {capture assign=infoMessage}{ts}Duplicate relationship.{/ts}{/capture}
                        {else}   
                          {capture assign=infoMessage}{ts}Too many matching results. Please narrow your search by entering a more complete target contact name.{/ts}{/capture}
                        {/if}  
                        {include file="CRM/common/info.tpl"}
                    {/if}
                {else} {* no valid matches for name + contact_type *}
                        </div></fieldset>
                        {capture assign=infoMessage}{ts 1=$form.name.value 2=$contact_type_display}No matching results for <ul><li>Name like: %1</li><li>Contact type: %2</li></ul>Check your spelling, or try fewer letters for the target contact name.{/ts}{/capture}
                        {include file="CRM/common/info.tpl"}                
                {/if} {* end if searchCount *}
              {else}
                </div></fieldset>
              {/if} {* end if searchDone *}
        {/if} {* end action = add *}

        {* Only show start/end date and buttons if action=update, OR if we have $contacts (results)*}
        {if $searchRows OR $action EQ 2}
            <div class="form-item">
                <dl>
                <dt>{$form.start_date.label}</dt>
                <dd>{$form.start_date.html} {include file="CRM/common/calendar/desc.tpl" trigger=trigger_relationship_1}
{include file="CRM/common/calendar/body.tpl" dateVar=start_date startDate=1985 endDate=2025 trigger=trigger_relationship_1}
                </dd>
                <dt>{$form.end_date.label}</dt>
                <dd>{$form.end_date.html}{include file="CRM/common/calendar/desc.tpl" trigger=trigger_relationship_2} 
{include file="CRM/common/calendar/body.tpl" dateVar=end_date startDate=1985 endDate=2025 trigger=trigger_relationship_2}
                </dd>
                <dt>{$form.description.label}</dt>
                <dd>{$form.description.html}</dd>
                <dt> </dt>
                    <dd class="description">
                        {ts}If this relationship has start and/or end dates, specify them here.{/ts}
                    </dd>
		<dt>{$form.note.label}</dt><dd>{$form.note.html}</dd>
        </dl>
	{include file="CRM/Contact/Page/View/CustomData.tpl" mainEditForm=1}
        <dl>
      	<dt></dt><dd>{$form.buttons.html}</dd>
                </dl>
            </div>
            </div></fieldset>
        {/if}
  
	
{/if}
{if $action eq 8}
     <fieldset><legend>{ts}Delete Relationship{/ts}</legend>
	<dl>
        <div class="status">
        {capture assign=relationshipsString}{$currentRelationships.$id.relation}{ $disableRelationships.$id.relation} {$currentRelationships.$id.name}{ $disableRelationships.$id.name }{/capture}
        {ts 1=$relationshipsString}Are you sure you want to delete the Relationship "%1"?{/ts}
        </div>
        <dt></dt>
        <dd>{$form.buttons.html}</dd>
    </dl>
 </fieldset>	
{/if}
