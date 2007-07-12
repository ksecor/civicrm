{* Main template for contact search *}
{include file="CRM/Contact/Form/Search/Intro.tpl"}

{if $context eq 'smog'}
    {if $rowsEmpty}
        {assign var="showBlock" value=""}
        {assign var="hideBlock" value="'searchForm','searchForm_show'"}
    {else}
        {assign var="showBlock" value="'searchForm_show'"}
        {assign var="hideBlock" value="'searchForm'"}
    {/if}
{else}
    {assign var="showBlock" value="'searchForm'"}
    {assign var="hideBlock" value="'searchForm_show'"}
{/if}

{* This section handles form elements for search criteria *}
<div id="searchForm_show" class="form-item">
  <a href="#" onclick="hide('searchForm_show'); show('searchForm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
  <label>
        {if $context EQ 'smog'}{ts}Find Members within this Group{/ts}
        {elseif $context EQ 'amtg'}{ts}Find Contacts to Add to this Group{/ts}
        {else}{ts}Search Criteria{/ts}{/if}
  </label>
</div>

<div id="searchForm">
    {include file="CRM/Contact/Form/Search/BasicCriteria.tpl"}
</div>

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
<script type="text/javascript">
    var showBlock = new Array({$showBlock});
    var hideBlock = new Array({$hideBlock});

{* hide and display the appropriate blocks *}
    on_load_init_blocks( showBlock, hideBlock );
</script>
