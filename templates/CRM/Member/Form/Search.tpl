<div id="help">
    {ts}Use this form to find member(s) by member name,email,membership type, status, source, signup /renew date,end date.{/ts}
</div>
<div class="form-item">
<fieldset><legend>{ts}Find Members{/ts}</legend>
    {strip} 
        <dl>
        <dt>{$form.sort_name.label}</dt>
        <dd>
        <table class="form-layout">
        <tr>
	<td>{$form.sort_name.html}
	    <div class="description font-italic">
                   {ts}Complete OR partial name OR email.{/ts}
            </div>		
	</td><td class="label">{$form.buttons.html}</td>
        </tr>
        </table>
        </dd>
        {include file="CRM/Member/Form/Search/Common.tpl"}
    {/strip}
</fieldset>
</div> 
{if $rowsEmpty}
    {include file="CRM/Member/Form/Search/EmptyResults.tpl"}
{/if}

{if $rows}
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
