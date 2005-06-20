<fieldset><legend>{ts}Custom Options{/ts}</legend>
	{strip}
	{*<table>
	<tr>
	    <th> {ts}Defaults{/ts}</th>
            <th> {ts}Label{/ts}</th>
            <th> {ts}Value{/ts}</th>
            <th> {ts}Weight{/ts}</th>
	    <th> {ts}Status?{/ts}</th>
	    <th> &nbsp;</th>
        </tr>
	<tr id="optrow[0]" class="{cycle values="odd-row,even-row"}">
	    <td> {$form.defaultoption[0].html}</td>
	    <td> {$form.optionlabel.0.html}</td>
            <td> {$form.optionvalue.0.html}</td>
            <td> {$form.optionweight.0.html}</td>
            <td> {$form.option_is_active.0.html}</td>
	    <td> &nbsp;</td>
	</tr>
	
	<div id="optionField[1][show]" class="add-remove-link">
 	{$form.optionField.1.show.html}
        </div>
	
	{section name=looper start=1 loop=5}
	{assign var=index value=$smarty.section.looper.index}	
	<tr id="optionField[{$index}]" class="form-item {cycle values="odd-row,even-row"}" style="display: none">
	    <td> {$form.defaultoption[$index].html}</td>
	    <td> {$form.optionlabel.$index.html}</td>
	    <td> {$form.optionvalue.$index.html}</td>
	    <td> {$form.optionweight.$index.html}</td>
 	    <td> {$form.option_is_active.$index.html}</td>
	</tr>
	<span id="optionField[{$index}][hide]" class="add-remove-link element-right">
                {$form.optionField.$index.hide.html}
            </span>
            {if $index LT 5}
            {assign var=j value=$index+1}
            	<div id="optionField[{$j}][show]" class="add-remove-link">
 			{$form.optionField.$j.show.html}
            	</div>
            {/if}
        	<!-- Spacer div contains floated elements -->
	    <div class="spacer"></div>
	
	{/section}
	</table>*}
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
</fieldset>


