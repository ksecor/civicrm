<fieldset><legend>{ts}Selection Options{/ts}</legend>
	{strip}
	<table>
	<tr><th>&nbsp;</th>
	    <th> {ts}Defaults{/ts}</th>
        <th> {ts}Label{/ts}</th>
        <th> {ts}Value{/ts}</th>
        <th> {ts}Weight{/ts}</th>
	    <th> {ts}Active?{/ts}</th>
    </tr>
	
	{section name=rowLoop start=1 loop=12}
	{assign var=index value=$smarty.section.rowLoop.index}
	<tr id="optionField[{$index}]" class="form-item {cycle values="odd-row,even-row"}">
        <td> 
        {if $index GT 1}
            <a onclick="hide('optionField[{$index}]'); return false;" name="optionField[1][hide]" href="#optionField[1]" id="optionField[1][hide]" class="form-link"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="hide field or section"></a>
            {* $form.optionField.$index.hide.html *}
        {/if}
        </td>
	    <td> {$form.default_option[$index].html}</td>
	    <td> {$form.option_label.$index.html}</td>
	    <td> {$form.option_value.$index.html}</td>
	    <td> {$form.option_weight.$index.html}</td>
 	    <td> {$form.option_status.$index.html}</td>
	</tr>
    {/section}
    </table>
    {* Set of divs for the 'show' next row links *}
	{section name=showLoop start=2 loop=12}
        {assign var=j value=$smarty.section.showLoop.index}
        <div id="optionField[{$j}][show]" class="add-remove-link">
            <a onclick="show('optionField[{$j}]','table-row'); show('optionField[{$j+1}][show]','table-row'); hide('optionField[{$j}][show]'); return false;" href="#optionField[j][show]" class="form-link"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="show field or section">another option row</a>
            {* $form.optionField.$j.show.html*}
        </div>
    {/section}
    {/strip}
    
</fieldset>
{assign var=showRows value="'optionField[1]','optionField[2]','optionField[3]'"}
{assign var=hideBlocks value="'optionField[2][show]','optionField[4][show]','optionField[5][show]','optionField[6][show]','optionField[7][show]','optionField[8][show]','optionField[9][show]','optionField[10][show]','optionField[11][show]','optionField[3]','optionField[4]','optionField[5]','optionField[6]','optionField[7]','optionField[8]','optionField[9]','optionField[10]','optionField[11]'"}
<script type="text/javascript">
    var showRows = new Array({$showRows});
    var hideBlocks = new Array({$hideBlocks});

    {* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showRows, hideBlocks, 'table-row' );
    show('optionField[3][show]');
</script>
