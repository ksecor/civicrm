{if $products}
<div id="premiums">
    {if $context EQ "makeContribution"}

{literal}
<script type="text/javascript">
<!--
// Selects the product (radio button) if user selects an option for that product.
// Putting this function directly in template so they are available for standalone forms.
function selectPremium(optionField) {
    premiumId = optionField.name.slice(8);
    for( i=0; i < document.Main.elements.length; i++) {
        if (document.Main.elements[i].type == 'radio' && document.Main.elements[i].name == 'selectProduct' && document.Main.elements[i].value == premiumId ) {
            element = document.Main.elements[i];
            element.checked = true;
        }
    }
}
//-->
</script>
{/literal}

        <fieldset>
        <div class="form-item">
        {if $premiumBlock.premiums_intro_title}
            <legend>{$premiumBlock.premiums_intro_title}</legend>
        {/if}
        {if $premiumBlock.premiums_intro_text}
            <div id=premiums-intro>
                <p>{$premiumBlock.premiums_intro_text}</p>
            </div> 
        {/if}
    {/if}
    {if $context EQ "confirmContribution" OR $context EQ "thankContribution"}
        <div class="header-dark">
            {if $premiumBlock.premiums_intro_title}
                {$premiumBlock.premiums_intro_title}
            {else}
                {ts}Your Premium Selection{/ts}
            {/if}
        </div>
    {/if}
    {if $preview}
        {assign var="showSelectOptions" value="1"}
    {/if}
    {strip}
        <table id="premiums-listings" class="no-border">
        {foreach from=$products item=row}
        <tr {if $context EQ "makeContribution"}class="odd-row" {/if}valign="top"> 
            {if $showRadio }
                {assign var="pid" value=$row.id}
                <td>{$form.selectProduct.$pid.html}</td>
            {/if}
            <td>{if $row.thumbnail}<a href="javascript:popUp('{$row.image}')" ><img src="{$row.thumbnail}" alt="{$row.name}" class="no-border" /></a>{else}&nbsp;{/if}</td>    	
	        <td>
                <strong>{$row.product_name}</strong><br />
                {$row.description} &nbsp;
                {if ( ($premiumBlock.premiums_display_min_contribution AND $context EQ "makeContribution") OR $preview EQ 1) AND $row.min_contribution GT 0 }
                    {ts 1=$row.min_contribution|crmMoney}(Contribute at least %1 to be eligible for this gift.){/ts}
                {/if}
            {if $showSelectOptions }
                {assign var="pid" value="options_"|cat:$row.id}
                {if $pid}
                    <div id="product-options">
                      <p>{$form.$pid.html}</p>
                    </div>
                {/if}
            {else}
                <div id="product-options">
                    <p><strong>{$row.options}</strong></p> 
                </div>
            {/if}
            {if $context EQ "thankContribution" AND $is_deductible AND $row.price}
                <div id="premium-tax-disclaimer">
                <p>
                {ts 1=$row.price|crmMoney}The value of this premium is %1. This may affect the amount of the tax deduction you can claim. Consult your tax advisor for more information.{/ts}
                </p>
                </div>
            {/if}
            </td>
        </tr>
        {/foreach}
        {if $showRadio AND !$preview }
            <tr class="odd-row"><td colspan="4">{$form.selectProduct.no_thanks.html}</td></tr> 
        {/if}          
        </table>
    {/strip}
    {if $context EQ "makeContribution"}
        </fieldset>
    {/if}
</div>
{literal}
<script type="text/javascript">
function popUp(URL) {
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=640,height=420,left = 202,top = 184');");
}
</script>
{/literal}
{/if}

