
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
    <fieldset><legend>{if $action eq 2 }{ts}Add Products to this Page{/ts} { else } {ts}Remove Products from this Page{/ts}{/if}</legend>
   {if $action eq 8}
      <div class="messages status">
        <dl>
          <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
          <dd>    
          {ts}Are you sure you want to remove this premium product from this Contribution page ?{/ts}
          </dd>
       </dl>
      </div>
  {else}
    <dl>
    <dt>{$form.product_id.label}</dt><dd>{$form.product_id.html}</dd>
    <dt>{$form.sort_position.label}</dt><dd>{$form.sort_position.html}</dd>
    </dl>
  {/if}	
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
