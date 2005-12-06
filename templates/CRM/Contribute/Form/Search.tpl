<div id="help">
    {ts}Use this form to find contributions by contributor name, contribution date or amount ranges, type of contribution, payment method and / or status.{/ts}
</div>
<fieldset><legend>{ts}Find Contributions{/ts}</legend>
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
        <tr> 
            <td class="label"> 
                {$form.contribution_from_date.label} 
            </td>
            <td>
                {$form.contribution_from_date.html} &nbsp; 
            </td>
            <td colspan="2"> 
                 {$form.contribution_to_date.label} {$form.contribution_to_date.html} 
            </td> 
        </tr> 
        <tr> 
            <td class="label"> 
                {$form.contribution_min_amount.label} 
            </td> 
            <td>
                {$form.contribution_min_amount.html}
            </td> 
            <td colspan="2"> 
                  {$form.contribution_max_amount.label} {$form.contribution_max_amount.html} 
            </td> 
        </tr>
        <tr>
            <td class="label">{ts}Contribution Type{/ts}</td> 
            <td>{$form.contribution_type_id.html}</td> 
            <td class="label">{ts}Paid By{/ts}</td> 
            <td>{$form.payment_instrument_id.html}</td> 
        </tr>
        <tr>
            <td class="label">{ts}Status{/ts}</td> 
            <td colspan="3">{$form.contribution_status.html}</td>
        </tr>
        </table>
    {/strip}
</fieldset>

{if $rowsEmpty}
    {include file="CRM/Contribute/Form/Search/EmptyResults.tpl"}
{/if}

{if $rows}
    {* Search request has returned 1 or more matching rows. *}
    <fieldset>
    
       {* This section handles form elements for action task select and submit *}
       {include file="CRM/Contribute/Form/Search/ResultTasks.tpl"}

       {* This section displays the rows along and includes the paging controls *}
       <p></p>
       {include file="CRM/Contribute/Form/Selector.tpl"}
       
    </fieldset>
    {* END Actions/Results section *}

{/if}

{if ! empty( $rows )}
{/if}
