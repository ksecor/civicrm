{* this template is used for adding/editing dupe match *}
<div class="form-item">
{if $advance }
<fieldset><legend>{if $action eq 2}{ts} Advanced Duplicate Matching Rule {/ts}{elseif $action eq 8}{ts}Delete Duplicate Matching Rule{/ts}{/if}</legend>
{else}
<fieldset><legend>{if $action eq 2}{ts} Basic Duplicate Matching Rule {/ts}{elseif $action eq 8}{ts}Delete Duplicate Matching Rule{/ts}{/if}</legend>
{/if}
	{if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
          <dd>    
          {ts}Are you sure delete this Duplicate matching Rule{/ts}
          </dd>
       </dl>
      </div>
     {else}
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
	<dt></dt><dd>{$form.buttons.html}</dd>
</fieldset>
{if !$advance }
<a href="{crmURL q="action=update&reset=1&advance=1"}" id="newDupeMatch">&raquo; {ts}Advanced Configuration{/ts}</a>
{/if}
</div>
