{* Main template for contact search *}

{include file="CRM/Contact/Form/Search/Intro.tpl"}

{* This section handles form elements for search criteria *}
{include file="CRM/Contact/Form/Search/BasicCriteria.tpl"}

{if $rowsEmpty}
    {include file="CRM/Contact/Form/Search/EmptyResults.tpl"}
{/if}

{if $rows}
    {* Search request has returned 1 or more matching rows. *}
    <fieldset>
    
       {* This section handles form elements for action task select and submit *}
       {include file="CRM/Contact/Form/Search/ResultTasks.tpl"}

       {* This section displays the rows along and includes the paging controls *}
       <p></p>
       {include file="CRM/Contact/Form/Selector.tpl"}
       

    </fieldset>
    {* END Actions/Results section *}

{/if}
