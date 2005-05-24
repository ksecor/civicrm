{* this template is used for adding/editing/viewing relationships  *}

{if $action eq 4} {* action = view *}
    <div class="form-item">
        <fieldset><legend>View Relationship</legend>

        <div class="form-item">
	    {foreach from=$viewRelationship item="row"}
            <dl>
            <dt>{$row.relation}</dt> 
            <dd class="label">{$row.name}</dd>
            {if $row.start_date}
                <dt>Start Date: </dt><dd>{$row.start_date|date_format:"%B %e, %Y"}</dd>
            {/if}
            {if $row.end_date}
                <dt>End Date: </dt><dd>{$row.end_date|date_format:"%B %e, %Y"}</dd>
            {/if}
            <dt>Status:</dt><dd>{if $row.is_active}Enabled {else} Disabled{/if}</dd>
            <dt></dt>
            <dd><input type="button" name='cancel' value="Done" onClick="location.href='{crmURL p='civicrm/contact/view/rel' q='action=browse'}';"></dd>
            </dl>
            {/foreach}
        </div>
        </fieldset>
     </div>    
        
{else} {* add and update actions *}
    <fieldset><legend>{if $action eq 1}{ts}New Relationship{/ts}{else}{ts}Edit Relationship(s){/ts}{/if}</legend>
        <div class="form-item">
            {if $action eq 1}
                <div class="description">
                {ts}Select the relationship type. Then locate target contact(s) for this relationship by entering a full or partial name, selecting the target contact type and clicking 'Search'.{/ts}
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
                <dt>{$form.contact_type.label}</dt><dd>{$form.contact_type.html}</dd>
                <dt> </dt>
                  <dd>
                    {$form._qf_Relationship_refresh.html}
                    {$form._qf_Relationship_cancel.html}
                  </dd>
                </dl>

              {if $noResult }
                 <div class="message status">{$noResult}</div>
              {else}
                 {if $contacts }
                    <fieldset><legend>{ts}Mark Target Contact(s) for this Relationship{/ts}</legend>
                    <div class="description">
                        {ts}Mark the target contact(s) for this relationship if it appears below. Otherwise you may
                        modify the Target Contact Name or Contact Type and click Search again.{/ts}
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
                    {foreach from=$contacts item="row"}
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
                 {else}
                    {if $noContacts}
                        <div class="message status"> {$noContacts} </div>
                    {/if}
                 {/if}
              {/if}
        {/if} {* end action = add *}

        {* Only show start/end date and buttons if action=update, OR if we have $contacts (results)*}
        {if $contacts OR $action EQ 2}
            <div class="form-item">
                <dl>
                <dt>{$form.start_date.label}</dt><dd>{$form.start_date.html}</dd>
                <dt>{$form.end_date.label}</dt><dd>{$form.end_date.html}</dd>
                <dt> </dt>
                    <dd class="description">
                        {ts}If this relationship has start and/or end dates, specify them here.{/ts}
                    </dd>
                <dt></dt><dd>{$form.buttons.html}</dd>
                </dl>
            </div>
        {/if}
        </div>
    </fieldset>
{/if}
