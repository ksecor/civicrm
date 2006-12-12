{* this template is used for adding event  *}
{include file="CRM/common/WizardHeader.tpl"}

<div class="form-item">
<fieldset><legend>{ts}Event Fees{/ts}</legend>
      <dl>
 	<dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    	<dt>{$form.paid_event.label}</dt><dd>{$form.paid_event.html}</dd>
    	<dt>{$form.contribution_type_id.label}</dt><dd>{$form.contribution_type_id.html}</dd>
    <div id="map-field">
    <p>{ts}Use the table below to enter up to ten fixed contribution amounts. These will be presented as a list of radio button options. Both the label and dollar amount will be displayed.{/ts}</p>
    <table id="map-field-table">
    <tr class="columnheader"><th scope="column">{ts}Contribution Label{/ts}</th><th scope="column">{ts}Amount{/ts}</th><th scope="column">{ts}Default?{/ts}</th></tr>
    {section name=loop start=1 loop=11}
       {assign var=idx value=$smarty.section.loop.index}
       <tr><td class="even-row">{$form.label.$idx.html}</td><td>{$config->defaultCurrencySymbol}&nbsp;{$form.value.$idx.html}</td><td class="even-row">{$form.default.$idx.html}</td></tr>
    {/section}
    </table>
    </div>
      </dl> 
    <dl>   
      <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
