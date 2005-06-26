{* Master tpl for Advanced Search *}

{include file="CRM/Contact/Form/Search/Intro.tpl"}

{assign var="showBlocks" value="'searchForm'"}
{assign var="hideBlocks" value="'searchForm[show]','searchForm[hide]'"}

<div id="searchForm[show]" class="form-item">
  <a href="#" onClick="hide('searchForm[show]'); show('searchForm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"></a><label>{ts}Edit Search Criteria{/ts}</label>
</div>

<div id="searchForm">
    {include file="CRM/Contact/Form/Search/AdvancedCriteria.tpl"}
</div>

{if $rowsEmpty}
    {include file="CRM/Contact/Form/Search/EmptyResults.tpl"}
{/if}

{if $rows}
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlocks" value="'searchForm[show]'"}
    {assign var="hideBlocks" value="'searchForm'"}
    
    <fieldset>
    
       {* This section handles form elements for action task select and submit *}
       {include file="CRM/Contact/Form/Search/ResultTasks.tpl"}

       {* This section displays the rows along and includes the paging controls *}
       <p>
       {include file="CRM/Contact/Form/Selector.tpl"}
       </p>

    </fieldset>
    {* END Actions/Results section *}

{/if}

<script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
</script>

