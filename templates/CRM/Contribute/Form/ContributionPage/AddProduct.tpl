
{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/WizardHeader.tpl}
<div id="help">
    {if $action eq 0}
        <p>{ts}need to add discription ....{/ts}</p>
    {else}
        {ts}Use this form to add  products for this contribution Page .{/ts}
    {/if}
</div>
 
<div class="form-item">
    <fieldset><legend>{ts}Add Products to this Page{/ts}</legend>
    <dl>
    <dt>{$form.product_id.label}</dt><dd>{$form.product_id.html}</dd>
    <dt>{$form.sort_position.label}</dt><dd>{$form.sort_position.html}</dd>
    </dl>
    </fieldset>
</div>

{if $action ne 4}
<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>
{else}
    <div id="crm-done-button">
        {$form.done.html}
    </div>
{/if} {* $action ne view *}
