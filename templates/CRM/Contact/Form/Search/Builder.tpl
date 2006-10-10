{* Search Builder *}

<div id="help">
{ts 1="http://wiki.civicrm.org/confluence//x/si"}
<p><strong>IMPORTANT: Search Builder requires you to use specific formats for your search values. Review the <a href="%1">Search Builder documentation</a> before building your first search...</strong></p>
<p>Create your search by selecting the criteria (record type and field), the comparison operator, and entering the value you want to search for. You can define one or many criteria as a set:
  <em>Include contacts where...State IS Washington AND City IS Seattle AND Birth Date is later than (>) Jan 1, 1985</em></p>
<p>You can also create additional sets of criteria: <em>Also include contacts where...State IS California AND City IS Los Angeles AND Birth Date is later than (>) Jan 1, 1985</em></p>
{/ts}

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
