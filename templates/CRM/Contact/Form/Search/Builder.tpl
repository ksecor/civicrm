{* Search Builder *}
{assign var="showBlock" value="'searchForm'"}
{assign var="hideBlock" value="'searchForm_show'"}
<div id="help">
{capture assign=docLink}{docURL page="Search Builder" text="Search Builder Documentation"}{/capture}
<strong>{ts 1=$docLink}IMPORTANT: Search Builder requires you to use specific formats for your search values. Review the %1 before building your first search.{/ts}</strong> {help id='builder-intro'}
</div>
<div id="searchForm_show" class="form-item">
  <a href="#" onclick="hide('searchForm_show'); show('searchForm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
  <label>
        {ts}Edit Search Criteria{/ts}
  </label>
</div>
<div id = "searchForm">	
{* Table for adding search criteria. *}
{include file="CRM/Contact/Form/Search/table.tpl"}

<br clear="all" />
<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>
</div>
{if $rowsEmpty}
    {include file="CRM/Contact/Form/Search/EmptyResults.tpl"}
{/if}

{if $rows}
     {assign var="showBlock" value="'searchForm_show'"}
     {assign var="hideBlock" value="'searchForm'"}
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
<script type="text/javascript">
    var showBlock = new Array({$showBlock});
    var hideBlock = new Array({$hideBlock});

{* hide and display the appropriate blocks *}
    on_load_init_blocks( showBlock, hideBlock );
</script>
