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
	<tr id="optionField[{$index}]" class="form-item {cycle values="even-row,odd-row"}">
        <td> 
        {if $index GT 1}
             {$form.optionField.$index.hide.html}
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
            { $form.optionField.$j.show.html}
	    {if $j eq 11}
	    <div class="description">
		{ts}"If you need additional options - you can add them after you save your current entries."{/ts}
	    </div>
	    {/if}
        </div>
    {/section}
    {/strip}

</fieldset>
<script type="text/javascript">
    var showRows = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

    {* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showRows, hideBlocks, 'table-row' );
    show('optionField[3][show]');
</script>
