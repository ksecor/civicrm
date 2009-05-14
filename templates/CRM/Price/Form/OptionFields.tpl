<fieldset><legend>{ts}Multiple Choice Options{/ts}</legend>
    <div class="description">
        {ts}Enter up to ten (10) multiple choice options in this table (click 'another choice' for each additional choice). If you need more than ten options, you can create an unlimited number of additional choices using the Edit Multiple Choice Options link after saving this new field. The option 'label' is displayed on the form, while the option 'value' is stored in the contact record. The label and value may be the same or different. Inactive options are hidden when the field is presented.{/ts}
    </div>
	{strip}
	<table>
	<tr><th>&nbsp;</th>
	<th> {ts}Default{/ts}</th>
        <th> {ts}Label{/ts}</th>
        <th> {ts}Amount{/ts}</th>
        <th> {ts}Weight{/ts}</th>
	<th> {ts}Active?{/ts}</th>
    </tr>
	
	{section name=rowLoop start=1 loop=12}
	{assign var=index value=$smarty.section.rowLoop.index}
	<tr id="optionField_{$index}" class="form-item {cycle values="odd-row,even-row"}">
        <td> 
        {if $index GT 1}
            <a onclick="hiderow('optionField_{$index}', 'optionField'); return false;" name="optionField_{$index}" href="#optionField_{$index}" class="form-link"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}hide field or section{/ts}"/></a>
        {/if}
        </td>
	    <td> 
		<div id="radio{$index}" style="display:none">
		     {$form.default_option[$index].html} 
		</div>
		<div id="checkbox{$index}" style="display:none">
		     {$form.default_checkbox_option.$index.html} 
		</div>
	    </td>
	    <td> {$form.option_label.$index.html}</td>
	    <td> {$form.option_name.$index.html}</td>
	    <td> {$form.option_weight.$index.html}</td>
 	    <td> {$form.option_status.$index.html}</td>
	</tr>
    {/section}
    </table>
	<div id="optionFieldLink" class="add-remove-link">
        <a onclick="showrow('optionField',11); return false;" name="optionFieldLink" href="#optionFieldLink" class="form-link"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}show field or section{/ts}"/>{ts}another choice{/ts}</a>
    </div>
	<div id="additionalOption" class="description">
		{ts}If you need additional options - you can add them after you Save your current entries.{/ts}
	</div>
    {/strip}
    
</fieldset>
<script type="text/javascript">
    var showRows   = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});
    var rowcounter = 0;
    {literal}
    if (navigator.appName == "Microsoft Internet Explorer") {    
	for ( var count = 0; count < hideBlocks.length; count++ ) {
	    var r = document.getElementById(hideBlocks[count]);
            r.style.display = 'none';
        }
    }
    {/literal}
    {* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showRows, hideBlocks, '' );
</script>
