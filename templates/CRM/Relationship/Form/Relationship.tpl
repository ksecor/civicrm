{* this template is used for adding relationships  *}
<form {$form.attributes}>
<div class="form-item">
<fieldset><legend>{if $op eq 'add'}New{else}Edit{/if} Relationship(s)</legend>
	<div class="data-group">
      	<span><label>{$displayName} is {$form.relationship_type_id.html} of {$sort_name}</label></span>
	</div>
	<div>
	<span class="description">
	Use 'Search' to narrow down list of contacts. Then mark the contact(s) and click 'Create Relationship'	
	</span>

	{if $op eq 'add'}
	<p>
	<div class="horizontal-position">
	<span class="two-col1">
	<span class="labels"><label>Search:</label></span>
	<span class="fields">{$form.contact_type.html}</span> 
	</span>
	<div class="spacer"></div>
	</div>

	<div class="horizontal-position">
	<span class="two-col1">
        <span class="fields">{$form.name.html}</span>
	</span>
	<span class="two-col2">
	<span>
          <input type="button" name='search' value="Search" onClick="'{$form.formName}.csearch.value=1';{$form.formName}.submit();">
        </span>
	</span> 
	<div class="spacer"></div>
	</div>
	</p>

	  <div class="form-item">
    	  <fieldset>
	  {if ($noResult) }
	     <div class="message status">{$noResult}</div>
          {else}
	     {if ($contacts) }
	       {foreach from=$contacts item="row"}
 	       {$form.contact_check[$row.id].html}
                &nbsp;{$row.type} &nbsp;{$row.name} <br>
	       {/foreach}
             {else}
		{if $noContacts}
	     	<div class="message status"> {$noContacts} </div>
                {/if}
             {/if}
          {/if}
    	  </fieldset>
	  </div>
	{/if}

	<div class="horizontal-position">
	<span class="two-col1">
	<span class="labels">{$form.start_date.label}</span>
	<span class="fields">{$form.start_date.html}</span>
	</span>
	<div class="spacer"></div>
	</div>

	<div class="horizontal-position">
	<span class="two-col1">
	<span class="labels">{$form.end_date.label}</span>
	<span class="fields">{$form.end_date.html}</span>
	</span>      
	<div class="spacer"></div>
	</div>
	
	<div class="horizontal-position">
	<span class="two-col1">
        <span class="fields">{$form.buttons.html}</span>
	</span>
	<div class="spacer"></div>
	</div>
    </fieldset>
</div>
</form>
