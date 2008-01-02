<div id="help">
    {ts}Use this form to find member(s) by member name or email address, membership type, status, source, and/or membership period start and end dates. Multiple selections for Membership Type and Status are combined as OR criteria (e.g. checking "Membership Type A" and "Membership Type B" will find contacts who have either membership). All other search fields are combined as AND criteria (e.g. selecting Status is "Expired" AND Source is "Phone-banking" returns only those contacts who meet both criteria).{/ts}
</div>
{assign var="showBlock" value="'searchForm'"}
{assign var="hideBlock" value="'searchForm_show'"}

<div id="searchForm_show" class="form-item">
  <a href="#" onclick="hide('searchForm_show'); show('searchForm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
  <label>
        {ts}Edit Search Criteria{/ts}
  </label>
</div>

<div id="searchForm">
<fieldset><legend>{ts}Find Members{/ts}</legend>
{strip}
     <table class="form-layout">
		<tr>
            <td class="font-size12pt label">{$form.sort_name.label}</td>
            <td>{$form.sort_name.html|crmReplace:class:'twenty'}
                <div class="description font-italic">
                    {ts}To search by first AND last name, enter 'lastname, firstname'. Example: 'Doe, Jane'. For partial name search, use '%partialname' ('%' equals 'begins with any combination of letters').{/ts}
                </div>
            </td>
            <td>{$form.buttons.html}</td>       
        </tr>

        {include file="CRM/Member/Form/Search/Common.tpl"}

        <tr>
            <td colspan="2">&nbsp;</td>
            <td>{$form.buttons.html}</td>
        </tr>
    </table>
{/strip} 
</fieldset>
</div>

{if $rowsEmpty}
    {include file="CRM/Member/Form/Search/EmptyResults.tpl"}
{/if}

{if $rows}
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlock" value="'searchForm_show'"}
    {assign var="hideBlock" value="'searchForm'"}
    
    {* Search request has returned 1 or more matching rows. *}
    <fieldset>
    
       {* This section handles form elements for action task select and submit *}
       {include file="CRM/Member/Form/Search/ResultTasks.tpl"}

       {* This section displays the rows along and includes the paging controls *}
       <p></p>
       {include file="CRM/Member/Form/Selector.tpl" context="Search"}
       
    </fieldset>
    {* END Actions/Results section *}

{/if}

<script type="text/javascript">
    var showBlock = new Array({$showBlock});
    var hideBlock = new Array({$hideBlock});

{* hide and display the appropriate blocks *}
    on_load_init_blocks( showBlock, hideBlock );
</script>
