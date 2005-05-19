{* this template is used for adding/editing/viewing relationships  *}

{if $action eq 4} {* action = view *}
    <div class="form-item">
        <fieldset><legend>View Relationship</legend>

        <div class="form-item">
            <dl>
            <dt>{$relationship_name}</dt> 
            <dd class="label">{$relationship_contact_name}</dd>
            {if $start_date}
                <dt>Start Date: </dt><dd>{$start_date|date_format:"%B %e, %Y"}</dd>
            {/if}
            {if $end_date}
                <dt>End Date: </dt><dd>{$end_date|date_format:"%B %e, %Y"}</dd>
            {/if}
            <dt></dt>
            <dd><input type="button" name='cancel' value="Done" onClick="location.href='{crmURL p='civicrm/contact/view/rel' q='action=browse'}';"></dd>
            </dl>
        </div>
        </fieldset>
     </div>    
        
{else} {* add and update actions *}
    <fieldset><legend>{if $action eq 1}{ts}New Relationship{/ts}{else}{ts}Edit Relationship(s){/ts}{/if}</legend>
        <div class="data-group">
            {ts 1=$displayName 2=$form.relationship_type_id.html}<label>%1</label> &nbsp; %2 &nbsp; {/ts}{if $action EQ 2}{$sort_name}{else}...{/if}
        </div>
        {if $action eq 1} {* action = add *}
            <div class="form-item">
                <div class="description">
                    {ts}Locate target contact(s) for this relationship by entering a full or partial name, selecting the target contact type and clicking 'Search'.{/ts}
                </div>
                <dl>
                  <dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
                  <dt>{$form.contact_type.label}</dt><dd>{$form.contact_type.html}</dd>
                  <dt></dt>
                  <dd>
                 {$form.search.html}
                 {$form.cancel.html}
                  </dd>
                </dl>
            </div>

              {if $noResult }
                 <div class="message status">{$noResult}</div>
              {else}
                 {if $contacts }
                    <fieldset><legend>{ts}Search Results{/ts}</legend>
                    <div class="description">
                        {ts}Now mark the target contact(s) and click 'Create Relationship'.
                        You may optionally specify start and/or end dates if this relationship is time-delimited.{/ts}
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
            <dt></dt><dd>{$form.buttons.html}</dd>
            </dl>
        {/if}
    </fieldset>
{/if}
