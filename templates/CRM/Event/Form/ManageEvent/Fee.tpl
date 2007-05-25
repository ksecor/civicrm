{* this template is used for adding event  *}
{include file="CRM/common/WizardHeader.tpl"}
{capture assign="adminPriceSets"}{crmURL p='civicrm/admin/price' q="reset=1"}{/capture}

<div class="form-item">
<fieldset><legend>{ts}Event Fees{/ts}</legend>
    <dl>
    <dt>{$form.title.label}</dt><dd>{$form.title.html}</dd>
    <dt>{$form.is_monetary.label}</dt><dd>{$form.is_monetary.html}</dd>
    </dl>
  								
    <div id="event-fees">
        <div id="contributionType">
            <dl>
            <dt>{$form.contribution_type_id.label}</dt><dd>{$form.contribution_type_id.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}This contribution type will be assigned to payments made by participants when they register online.{/ts}
            <dt>{$form.fee_label.label}<span class="marker"> *</span>
	    </dt><dd>{$form.fee_label.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts}This label is displayed with the list of event fees.{/ts}
            </dl>
        </div>

        <div id="priceSet">
            <dl>
            <dt>{$form.price_set_id.label}</dt><dd>{$form.price_set_id.html}</dd>
            <dt>&nbsp;</dt><dd class="description">{ts 1=$adminPriceSets}Select a pre-configured Price Set to offer multiple individually priced options for event registrants. Otherwise, select &quot;- none -&quot, and enter one or more fee levels in the table below. Create or edit Price Sets <a href="%1">here</a>.{/ts}</dd>
            </dl>
        </div>
        
        <fieldset id="map-field">
        <p>{ts}Use the table below to enter up to ten fixed contribution amounts. These will be presented as a list of radio button options. Both the label and dollar amount will be displayed.{/ts}</p>
        <table id="map-field-table">
        <tr class="columnheader"><th scope="column">{ts}Fee Label{/ts}</th><th scope="column">{ts}Amount{/ts}</th><th scope="column">{ts}Default?{/ts}</th></tr>
        {section name=loop start=1 loop=11}
           {assign var=idx value=$smarty.section.loop.index}
           <tr><td class="even-row">{$form.label.$idx.html}</td><td>{$config->defaultCurrencySymbol}&nbsp;{$form.value.$idx.html}</td><td class="even-row">{$form.default.$idx.html}</td></tr>
        {/section}
        </table>
        </fieldset>
    </div>
    
    <dl>   
      <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>

{include file="CRM/common/showHide.tpl"}
