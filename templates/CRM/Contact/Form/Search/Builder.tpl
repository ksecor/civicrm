{* Search Builder *}
<div id="help">
<p><strong>{ts 1="http://wiki.civicrm.org/confluence//x/si"}IMPORTANT: Search Builder requires you to use specific formats for your search values. Review the <a href="%1">Search Builder documentation</a> before building your first search.{/ts}</strong> {help id='builder-intro'}</p>
</div>

{* Table for adding search criteria. *}
{include file="CRM/Contact/Form/Search/table.tpl"}

<br clear="all" />
<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>

{if $rowsEmpty}
    {include file="CRM/Contact/Form/Search/EmptyResults.tpl"}
{/if}

{if $rows}
    
    <fieldset>
    
       {* This section handles form elements for action task select and submit *}         
       {include file="CRM/Contact/Form/Search/ResultTasks.tpl"}

       {* This section displays the rows along and includes the paging controls *}
       <p></p>
       {include file="CRM/Contact/Form/Selector.tpl"}
      

    </fieldset>
    {* END Actions/Results section *}

{/if}

{$initHideBoxes}
