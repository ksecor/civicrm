<fieldset><legend>{ts}Multiple Choice Options{/ts}</legend>
    <div class="description">
        {ts}Enter up to ten (10) multiple choice options in this table (click 'another choice' for each additional choice). If desired, you can mark one of the choices as the default choice. The option 'label' is displayed on the form, while the option 'value' is stored in the contact record. The label and value may be the same or different. Inactive options are hidden when the field is presented.{/ts}
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
            <a onclick="hiderow('optionField[{$index}]'); return false;" name="optionField[{$index}]" href="#optionField[{$index}]" class="form-link"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}hide field or section{/ts}"></a>
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
	<div id="optionFieldLink" class="add-remove-link">
            <a onclick="showrow(); return false;" name="optionFieldLink" href="#optionFieldLink" class="form-link"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}show field or section{/ts}">{ts}another choice{/ts}</a>
        </div>
	<div id="additionalOption" class="description">
		{ts}"If you need additional options - you can add them after you Save your current entries."{/ts}
	</div>
    {/strip}
    
</fieldset>
{*assign var=showRows value="'optionField[1]','optionField[2]'"*}
{*assign var=hideBlocks value="'optionField[3]','optionField[4]','optionField[5]','optionField[6]','optionField[7]','optionField[8]','optionField[9]','optionField[10]','optionField[11]','additionalOption'"*}
<script type="text/javascript">
    var showRows   = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});
    var rowcounter = 0;

    {* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showRows, hideBlocks, 'table-row' );
</script>
