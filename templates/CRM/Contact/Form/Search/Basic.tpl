{* Main template for basic search (Find Contacts) *}
{include file="CRM/Contact/Form/Search/Intro.tpl"}

{if $context eq 'smog'}
    {if $rowsEmpty}
        {assign var="showBlock" value=""}
        {assign var="hideBlock" value="'searchForm','searchForm_show'"}
    {else}
        {assign var="showBlock" value="'searchForm_show'"}
        {assign var="hideBlock" value="'searchForm'"}
    {/if}
{/if}

{* This section handles form elements for search criteria *}
<div id="searchForm">
    {include file="CRM/Contact/Form/Search/BasicCriteria.tpl"}
</div>

{if $rowsEmpty}
    {include file="CRM/Contact/Form/Search/EmptyResults.tpl"}
{elseif $rows}    
    {* Search request has returned 1 or more matching rows. *}
    <fieldset>
    
       {* This section handles form elements for action task select and submit *}
       {include file="CRM/Contact/Form/Search/ResultTasks.tpl"}

       {* This section displays the rows along and includes the paging controls *}
       <p></p>
       {include file="CRM/Contact/Form/Selector.tpl"}
       

    </fieldset>
    {* END Actions/Results section *}
{else}
    <div class="spacer">&nbsp;</div>
{/if}
