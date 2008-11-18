{* template to display reports *}
{if $report}
{$report}
{else}
<div id="reportForm" class="form-item">
<fieldset><legend>{ts}Report Parameters{/ts}</legend>
    {strip} 
        <table class="form-layout">
        <tr>
           <td>
               {$form.include_activities.label}
           </td>       
           <td>
               {$form.include_activities.html}
           </td>       
        </tr>
        <tr>
           <td>
	       &nbsp;
           </td>       
           <td>
               {$form.is_redact.html}&nbsp;{$form.is_redact.label}
           </td>       
        </tr>
        <tr>
           <td colspan="2">{$form.buttons.html}</td>
        </tr>
        </table>
    {/strip}
</fieldset>
</div>
{/if}
