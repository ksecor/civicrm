{* this template is used for adding/editing relationships  *}

<form {$form.attributes}>
<fieldset><legend>{if $action eq 1}New{else}Edit{/if} Relationship(s)</legend>
	<div class="data-group">
      	<label>{$displayName}</label> is a(n) &nbsp; {$form.relationship_type_id.html} &nbsp; of {if $action EQ 2}{$sort_name}{else}...{/if}
	</div>
	{if $action eq 1} {* action = add *}
        <div class="form-item">
            <div class="description">
                {t}Locate target contact(s) for this relationship by entering a full or partial name, selecting the target contact type and clicking 'Search'.{/t}
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
               <fieldset><legend>Search Results</legend>
                <div class="description">
                    {t}Now mark the target contact(s) and click 'Create Relationship'.
                    You may optionally specify start and/or end dates if this relationship is time-delimited.{/t}
                </div>
               {foreach from=$contacts item="row"}
               {$form.contact_check[$row.id].html}
                    &nbsp;{$row.type} &nbsp;{$row.name} &nbsp;{$row.city}&nbsp;{$row.state}&nbsp;{$row.email}&nbsp;{$row.phone}<br>
               {/foreach}
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
</form>
