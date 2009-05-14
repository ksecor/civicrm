
{assign var="showBlock" value="'searchForm'"}
{assign var="hideBlock" value="'searchForm_show'"}
<div id="searchForm_show" class="form-item">
  <a href="#" onclick="hide('searchForm_show'); show('searchForm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
  <label>
        {ts}Edit Search Criteria{/ts}
  </label>
</div>
{* Search form and results for Grants *}
<div id="help">
    {ts}Use this form to find Grant(s) by Contact name, Grant Status, Grant Type, Total Amount , etc .{/ts}
</div>
<div id="searchForm" class="form-item">
<fieldset><legend>{ts}Find Grants{/ts}</legend>
<div class="form-item">
{strip} 
        <table class="form-layout">
		<tr>
            <td class="font-size12pt label">{$form.sort_name.label}</td>
            <td colspan="4">{$form.sort_name.html} {$form.buttons.html}
                <div class="description font-italic">
                    {ts}Complete OR partial name OR email.{/ts}
                </div>
            </td>
        </tr>

        {include file="CRM/Grant/Form/Search/Common.tpl"}

        </table>
    {/strip}
</div> 
</fieldset>
</div>
{if $rowsEmpty}
    {include file="CRM/Grant/Form/Search/EmptyResults.tpl"}
{/if}

{if $rows}
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlock" value="'searchForm_show'"}
    {assign var="hideBlock" value="'searchForm'"}
    {* Search request has returned 1 or more matching rows. *}
    <fieldset>
    
       {* This section handles form elements for action task select and submit *}
       {include file="CRM/common/searchResultTasks.tpl"}

       {* This section displays the rows along and includes the paging controls *}
       <p></p>
       {include file="CRM/Grant/Form/Selector.tpl" context="Search"}
       
    </fieldset>
    {* END Actions/Results section *}

{/if}
<script type="text/javascript">
    var showBlock = new Array({$showBlock});
    var hideBlock = new Array({$hideBlock});

{* hide and display the appropriate blocks *}
    on_load_init_blocks( showBlock, hideBlock );
</script>
