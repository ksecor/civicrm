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
            <td colspan="2">{$form.sort_name.html}
                <div class="description font-italic">
                    {ts}Complete OR partial name OR email.{/ts}
                </div>
            </td>
            <td class="label">{$form.buttons.html}</td>       
        </tr>

        {include file="CRM/Event/Form/Search/Common.tpl"}

        </table>
    {/strip}
</div> 
</fieldset>