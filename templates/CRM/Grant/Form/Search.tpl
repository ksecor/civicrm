{* Search form and results for Grants *}
<div id="help">
    {ts}Use this form to find Grant(s) by Contact name, Grant Status, Grant Type, Total Amount , etc .{/ts}
</div>
<fieldset><legend>{ts}Find Grants{/ts}</legend>
<div class="form-item">
{strip} 
        <table class="form-layout">
		<tr>
            <td class="font-size12pt label">{$form.sort_name.label}</td>
            <td colspan="2">{$form.sort_name.html}
                <div class="description font-italic">
                    {ts}Complete OR partial name OR email.{/ts}
                </div>
            </td>
            <td class="label">{$form.buttons.html}</td>       
        </tr>

        {include file="CRM/Grant/Form/Search/Common.tpl"}

        </table>
    {/strip}
</div> 
</fieldset>

{if $rowsEmpty}
    {include file="CRM/Grant/Form/Search/EmptyResults.tpl"}
{/if}

{if $rows}
    {* Search request has returned 1 or more matching rows. *}
    <fieldset>
    
       {* This section handles form elements for action task select and submit *}
       {include file="CRM/Grant/Form/Search/ResultTasks.tpl"}

       {* This section displays the rows along and includes the paging controls *}
       <p></p>
       {include file="CRM/Grant/Form/Selector.tpl" context="Search"}
       
    </fieldset>
    {* END Actions/Results section *}

{/if}
