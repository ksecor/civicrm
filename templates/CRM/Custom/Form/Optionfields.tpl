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
	<div id="optrow[0]">
	    <span class="fcol1"> {$form.defaultoption[0].html}</span>
	    <span class="fcol2"> {$form.optionlabel.0.html}</span>
            <span class="fcol3"> {$form.optionvalue.0.html}</span>
            <span class="fcol4"> {$form.optionweight.0.html}</span>
            <span class="fcol5"> {$form.option_is_active.0.html}</span>
	</div>
	<div id="optrow[1]">
	    <span class="fcol1"> {$form.defaultoption[1].html}</span>
	    <span class="fcol2"> {$form.optionlabel.1.html}</span>
            <span class="fcol3"> {$form.optionvalue.1.html}</span>
            <span class="fcol4"> {$form.optionweight.1.html}</span>
            <span class="fcol5"> {$form.option_is_active.1.html}</span>
	</div>
	<div id="optrow[2]">
	    <span class="fcol1"> {$form.defaultoption[2].html}</span>
	    <span class="fcol2"> {$form.optionlabel.2.html}</span>
            <span class="fcol3"> {$form.optionvalue.2.html}</span>
            <span class="fcol4"> {$form.optionweight.2.html}</span>
            <span class="fcol5"> {$form.option_is_active.2.html}</span>
	<div id="optionField[3][show]" class="add-remove-link">
 	{$form.optionField.3.show.html}
        </div>
	</div>
	
	{section name=looper start=3 loop=11}
	{assign var=index value=$smarty.section.looper.index}	
	<div id="optionField[{$index}]" class="form-item" style="display: none">
	    <span class="fcol1"> {$form.defaultoption[$index].html}</span>
	    <span class="fcol2"> {$form.optionlabel.$index.html}</span>
	    <span class="fcol3"> {$form.optionvalue.$index.html}</span>
	    <span class="fcol4"> {$form.optionweight.$index.html}</span>
 	    <span class="fcol5"> {$form.option_is_active.$index.html}</span>
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


