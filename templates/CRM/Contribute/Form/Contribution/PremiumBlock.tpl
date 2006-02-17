{if $products}
<div id="premiums">
{if $premiumBlock.premiums_intro_title }
<fieldset><legend>{$premiumBlock.premiums_intro_title}</legend>
{/if}
<div id=premiums-intro>
{$premiumBlock.premiums_intro_text}
</div> 

<p></p>
    <div class="form-item">
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Name{/ts}</th>
            <th>{ts}Image{/ts}</th>
            <th>{ts}SKU{/ts}</th>
            <th>{ts}Options{/ts}</th>
            <th>{ts}Price{/ts}</th>
            {if $premiumBlock.premiums_display_min_contribution}    
            <th>{ts}Min Contribution{/ts}</th>      
            {/if}
            {if $showRadio }
            <th>{ts}Select Product{/ts}</th>
            {/if}    
            </tr>
        {foreach from=$products item=row}
        <tr class="{cycle values="odd-row,even-row"}">
	        <td>{$row.name}</td>
            <td><a href="javascript:popUp('{$row.image}')"><img src="{$row.thumbnail}" ></a></td>    	
	        <td>{$row.sku}</td>
            {if $showSelectOptions }
            {assign var="pid" value=$row.id}
            <td>{$form.$pid.html}
            {else}
            <td>{$row.options}</td>
            {/if}
            <td>{$row.price }</td>
            {if $premiumBlock.premiums_display_min_contribution}
	        <td>{$row.min_contribution}</td>
            {/if}
            
            {if $showRadio }
            {assign var="pid" value=$row.id}
            <td>{$form.selectProduct.$pid.html}</td>
            {/if}

        </tr>
        {/foreach}
           
        </table>
        {if $showRadio }
        {$form.selectProduct.no_thanks.html} 
        {/if}
        {/strip}
        

 </fieldset>
</div>
{/if}

{literal}
<script type="text/javascript">
function popUp(URL) {
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=640,height=420,left = 202,top = 184');");
}
</script>
{/literal}
