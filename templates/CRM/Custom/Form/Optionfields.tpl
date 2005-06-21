<fieldset><legend>{ts}Selection Options{/ts}</legend>
	{strip}
	<table>
	<tr><th>&nbsp;</th>
	    <th> {ts}Defaults{/ts}</th>
        <th> {ts}Label{/ts}</th>
        <th> {ts}Value{/ts}</th>
        <th> {ts}Weight{/ts}</th>
	    <th> {ts}Status?{/ts}</th>
	    <th> &nbsp;</th>
    </tr>
	
	{section name=firstthree loop=3}
	{assign var=index value=$smarty.section.firstthree.index}
	<tr id="optionField[{$index}]" class="form-item {cycle values="odd-row,even-row"}">
        <td> 
            <a onclick="hide('optionField[{$index}]'); return false;" name="optionField[0][hide]" href="#optionField[0]" id="optionField[0][hide]" class="form-link"><img src="/dgg/drupal/modules/civicrm/i/TreeMinus.gif" class="action-icon" alt="hide field or section"></a>
            {* $form.optionField.$index.hide.html *}
        </td>
	    <td> {$form.default_option[$index].html}</td>
	    <td> {$form.option_label.$index.html}</td>
	    <td> {$form.option_value.$index.html}</td>
	    <td> {$form.option_weight.$index.html}</td>
 	    <td> {$form.option_status.$index.html}</td>
        <td> &nbsp;
            {if $index eq 2}
                {assign var=k value=$index+1}
                <span id="optionField[{$k}][show]" class="add-remove-link">
                    <a onclick="show('optionField[{$k}]','table-row');show('optionField[{$k}][show]'); return false;" name="optionField[3][show]" href="#optionField[3]" id="optionField[3][show]" class="form-link"><img src="/dgg/drupal/modules/civicrm/i/TreePlus.gif" class="action-icon" alt="show field or section">another row</a>
                    {* $form.optionField.$k.show.html *}
                </span>
            {/if}
        </td>
	</tr>
    {/section}

	{section name=looper start=3 loop=11}
	{assign var=index value=$smarty.section.looper.index}	
	<tr id="optionField[{$index}]" class="form-item {cycle values="odd-row,even-row"}" style="display: none">
	    <td> {$form.default_option[$index].html}</td>
	    <td> {$form.option_label.$index.html}</td>
	    <td> {$form.option_value.$index.html}</td>
	    <td> {$form.option_weight.$index.html}</td>
 	    <td> {$form.option_status.$index.html}</td>
        <td> 
            {if $index LT 11}
                {assign var=k value=$index+1}
                <span id="optionField[{$k}][show]" class="add-remove-link">
                    {$form.optionField.$k.show.html}
                </span>
            {/if}
        </td>
	</tr>
    {/section}

	</table>
    {/strip}
    
{*
	<div>
	    <span class="fcol1 label"> {ts}Defaults{/ts}</span>
            <span class="fcol2 label"> {ts}Label{/ts}</span>
            <span class="fcol3 label"> {ts}Value{/ts}</span>
            <span class="fcol4 label"> {ts}Weight{/ts}</span>
	    <span class="fcol5 label"> {ts}Status?{/ts}</span>
        </div>
	{section name=firstthree loop=3}
	{assign var=index value=$smarty.section.firstthree.index}
	<div id="optrow[{$index}]">
	    <span class="fcol1"> {$form.default_option[$index].html}</span>
	    <span class="fcol2"> {$form.option_label.$index.html}</span>
            <span class="fcol3"> {$form.option_value.$index.html}</span>
            <span class="fcol4"> {$form.option_weight.$index.html}</span>
            <span class="fcol5"> {$form.option_status.$index.html}</span>
            {if $index eq 2}
		{assign var=k value=$index+1}
	    <div id="optionField[{$k}][show]" class="add-remove-link">
		{$form.optionField.$k.show.html}
	    </div>
	    {/if}
        	<!-- Spacer div contains floated elements -->
	    <div class="spacer"></div>
	</div>
	{/section}

	{section name=looper start=3 loop=11}
	{assign var=index value=$smarty.section.looper.index}	
	<div id="optionField[{$index}]" class="form-item" style="display: none">
	    <span class="fcol1"> {$form.default_option[$index].html}</span>
	    <span class="fcol2"> {$form.option_label.$index.html}</span>
	    <span class="fcol3"> {$form.option_value.$index.html}</span>
	    <span class="fcol4"> {$form.option_weight.$index.html}</span>
 	    <span class="fcol5"> {$form.option_status.$index.html}</span>
	    <span id="optionField[{$index}][hide]" class="add-remove-link element-right">
                {$form.optionField.$index.hide.html}
            </span>
            {if $index LT 11}
            {assign var=j value=$index+1}
	       	<div id="optionField[{$j}][show]" class="add-remove-link">
			{$form.optionField.$j.show.html}
            	</div>
            {/if}
        	<!-- Spacer div contains floated elements -->
	    <div class="spacer"></div>
	</div>
	{/section}  
	{/strip}
    <div class="form-item">
	<a onclick="document.getElementById('showoption').style.display='none'">{ts}close{/ts}</a> 
    </div>
*}
</fieldset>


