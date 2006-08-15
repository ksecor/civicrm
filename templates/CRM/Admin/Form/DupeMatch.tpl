{* this template is used for adding/editing dupe matching rule *}
<div class="form-item">

{* Advanced rule editing is not yet implemented. *}
{if $advance }
    <fieldset><legend>{if $action eq 2}{ts}Advanced Duplicate Matching Rule{/ts}{elseif $action eq 8}{ts}Delete Duplicate Matching Rule{/ts}{/if}</legend>
{else}
    <fieldset><legend>{if $action eq 2}{ts}Configure Duplicate Matching Rule{/ts}{elseif $action eq 8}{ts}Delete Duplicate Matching Rule{/ts}{/if}</legend>
{/if}

{if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
          <dd>    
          {ts}Are you sure you want to delete this Duplicate Matching Rule?{/ts}
          </dd>
       </dl>
      </div>
{else}
    <div id="help">
    <p>{ts}Select up to five contact field(s) to use when comparing a new Individual contact to existing contact records for duplicate matching. If some of the fields in your matching rule are OPTIONAL, then all <strong>non-empty</strong> fields in the incoming data are used.{/ts}</p>
    <p>{ts}For example, using the default matching rule of <strong>Email AND First Name AND Last Name</strong>... Jane Doe jane.doe@example.org exists in your database. Now someone tries to add a new contact with the same email address and leaves the first and last name blank. The new contact will be flagged as a potential duplicate.{/ts}</p> 
    </div>
        <dl>
        {if $advance }
            <dt>{$form.match_on.label}</dt><dd>{$form.match_on.html|crmReplace:class:huge}</dd>
        {else}
             {section name = matchOnLoop start = 1 loop = 6}
                {assign var = count value = $smarty.section.matchOnLoop.index }
                {assign var = matchField  value = match_on_$count}	  
                <dt>{$form.$matchField.label}</dt><dd>{$form.$matchField.html}</dd> 
            {/section}
        {/if}
        </dl>
{/if}
<dl>
	<dt></dt><dd>{$form.buttons.html}</dd>
</dl>
</fieldset>

{if !$advance }
    {*<a href="{crmURL q="action=update&reset=1&advance=1"}" id="newDupeMatch">&raquo; {ts}Advanced Configuration{/ts}</a>*}
{/if}
</div>
