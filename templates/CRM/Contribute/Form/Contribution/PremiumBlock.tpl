{if $products}
<div id="premiums">
    {if $context EQ "makeContribution"}
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
            <td>{if $row.thumbnail}<a href="javascript:popUp('{$row.image}')"><img src="{$row.thumbnail}" alt="{$row.name}" border="0"></a>{else}&nbsp;{/if}</td>    	
	        <td>
                <strong>{$row.name}</strong><br />
                {$row.description} &nbsp;
                {if ($premiumBlock.premiums_display_min_contribution or $preview)  AND $row.min_contribution GT 0 }
                    {ts 1=$row.min_contribution|crmMoney}(Contribute at least %1 to be eligible for this gift.){/ts}
                {/if}
            </td>
            {if $showSelectOptions }
                {assign var="pid" value=$row.id}
                {if $pid}    
                <td>{$form.$pid.html}   
                {/if}
            {else}
                <td>{$row.options}</td>
            {/if}
        </tr>
        {/foreach}
        {if $showRadio }
            <tr><td colspan="4">{$form.selectProduct.no_thanks.html}</td></tr> 
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

