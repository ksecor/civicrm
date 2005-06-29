<fieldset><legend>{ts}Multiple Choice Options{/ts}</legend>
    <div class="description">
        {ts}Enter up to ten (10) multiple choice options in this table (click 'another choice' for each additional choice). If desired, you can mark one of the choices as the default choice. The option 'label' is displayed on the form, while the option 'value' is stored in the contact record. The label and value may be the same or different. Inactive options are hidden when the field is presented.{/ts}
	{strip}
	<table>
	<tr>
	    <th> {ts}Defaults{/ts}</th>
            <th> {ts}Label{/ts}</th>
            <th> {ts}Value{/ts}</th>
            <th> {ts}Weight{/ts}</th>
	    <th> {ts}Active?{/ts}</th>
	</tr>
	
	{section name=rowLoop start=1 loop=12}
	{assign var=index value=$smarty.section.rowLoop.index}
	<tr id="optionField[{$index}]" class="form-item {cycle values="odd-row,even-row"}">
	    <td> {$form.default_option[$index].html}</td>
	    <td> {$form.option_label.$index.html}</td>
	    <td> {$form.option_value.$index.html}</td>
	    <td> {$form.option_weight.$index.html}</td>
 	    <td> {$form.option_status.$index.html}</td>
	</tr>
    {/section}
    </table>
    {/strip}
	<div class="description" id="additionalOption">
	{ts}"If you need additional options - you can add them after you save your current entries."{/ts}
	</div>
    
</fieldset>

<script type="text/javascript">
    var showRows = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

    on_load_init_blocks( showRows, hideBlocks, 'table-row' );
</script>
