{* Master tpl for Advanced Search *}

{assign var="showBlocks" value="'searchForm'"}
{assign var="hideBlocks" value="'searchForm[show]','searchForm[hide]'"}

<form {$form.attributes}>
{$form.hidden}
<div id="searchForm[show]" class="form-item">
  <a href="#" onClick="hide('searchForm[show]'); show('searchForm'); return false;">(+)</a> <label>Search Criteria</label>
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
</form>

<script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
</script>

