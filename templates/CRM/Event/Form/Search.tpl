{* Search form and results for Events *}
<div id="help">
    {ts}Use this form to find participant(s) by participant name, event name, event start and end dates.{/ts}
</div>
<fieldset><legend>{ts}Find Participants{/ts}</legend>
<div class="form-item">
{strip} 
        <table class="form-layout">
		<tr>
            <td class="font-size12pt label">{$form.sort_name.label}</td>
            <td>{$form.sort_name.html|crmReplace:class:'twenty'}
                <div class="description font-italic">
                    {ts}Complete OR partial name OR email.{/ts}
                </div>
            </td>
            <td colspan="2">{$form.buttons.html}</td>       
        </tr>

        {include file="CRM/Event/Form/Search/Common.tpl"}
        
        <tr>
            <td colspan="2">&nbsp;</td>
            <td colspan="2">{$form.buttons.html}</td>
        </tr>
        </table>
    {/strip}
</div> 
</fieldset>

{if $rowsEmpty}
    {include file="CRM/Event/Form/Search/EmptyResults.tpl"}
{/if}

{if $rows}
    {* Search request has returned 1 or more matching rows. *}
    <fieldset>
    
       {* This section handles form elements for action task select and submit *}
       {include file="CRM/Event/Form/Search/ResultTasks.tpl"}

       {* This section displays the rows along and includes the paging controls *}
       <p></p>
       {include file="CRM/Event/Form/Selector.tpl" context="Search"}
       
    </fieldset>
    {* END Actions/Results section *}

{/if}
