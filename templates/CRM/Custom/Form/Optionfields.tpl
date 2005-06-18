<fieldset><legend>{ts}Custom Options{/ts}</legend>
	{strip}
	<table class="form-layout">
	<tr class="columnheader">
	    <th> {ts}Defaults{/ts}</th>
            <th> {ts}Label{/ts}</th>
            <th> {ts}Value{/ts}</th>
            <th> {ts}Weight{/ts}</th>
	    <th> {ts}Status?{/ts}</th>
	    <th> &nbsp;</th>
        </tr>
	<tr id="optrow0">
	    <td> {$form.defaultoption[0].html}</td>
	    <td> {$form.optionlabel.0.html}</td>
            <td> {$form.optionvalue.0.html}</td>
            <td> {$form.optionweight.0.html}</td>
            <td> {$form.option_is_active.0.html}</td>
	    <td> &nbsp;</td>
	</tr>
	{section name=looper start = 1 loop = 4}
	{assign var=index value=$smarty.section.looper.index}	
	<tr id="optionField[{$index}]">
	    <td> {$form.defaultoption[$index].html}</td>
	    <td> {$form.optionlabel.$index.html}</td>
            <td> {$form.optionvalue.$index.html}</td>
            <td> {$form.optionweight.$index.html}</td>
            <td> {$form.option_is_active.$index.html}</td>
	    <td> <div class="form-item">
	    	{if $index LT 4}
            	{assign var=j value=$index+1}
            	<div id="optionField[{$j}][show]" class="add-remove-link">
		{$form.optionField.$j.show.html}
            	</div>
            	{/if}
		</div>
		<div>
		<span id="optionField[{$index}][hide]" class="add-remove-link element-right">
                {$form.location.$index.hide.html}
        	</span>
        	<!-- Spacer div contains floated elements -->
	        <div class="spacer"></div>
		</div>
		</div>	
	</td>
	</tr>
	</tr>
	{/section}
	</table>
	{/strip}
	
<!-- Link to add another field.-->
        
{*{section name=rowadder start=1 loop=4}
	{assign var=index value=$smarty.section.rowadder.index}
	<div id="showrow{$index}">
            <span>
		{assign var=j value=$index+1}
	        <a onclick="show('optrow{$j}'); hide('showrow{$index}');return false;")">{ts}Add another row{/ts}</a>
	    </span>
	</div>
	{/section}
*}













{*
    <div class="form-item">
        {strip}
	<table class="form-layout">
	<tr class="columnheader">
	    <th> {ts}Defaults{/ts}</th>
            <th> {ts}Label{/ts}</th>
            <th> {ts}Value{/ts}</th>
            <th> {ts}Weight{/ts}</th>
	    <th> {ts}Status?{/ts}</th>
	    <th> &nbsp;</th>
        </tr>
	<tr id="optrow0">
	    <td> {$form.defaultoption[0].html}</td>
	    <td> {$form.optionlabel.0.html}</td>
            <td> {$form.optionvalue.0.html}</td>
            <td> {$form.optionweight.0.html}</td>
            <td> {$form.option_is_active.0.html}</td>
	    <td> &nbsp;</td>
	</tr>
	{section name=looper start=1 loop=4}
	 {assign var=index value=$smarty.section.looper.index}
	<tr id="optrow{$index}" style="display: none">
	    <td> {$form.defaultoption[$index].html}</td>
	    <td> {$form.optionlabel.$index.html}</td>
            <td> {$form.optionvalue.$index.html}</td>
            <td> {$form.optionweight.$index.html}</td>
            <td> {$form.option_is_active.$index.html}</td>
	    {assign var=j value=$index+1}
	    <td> <a onclick="hide('optrow{$index}');show('showrow{$index}');return false;">{ts}Hide this row{/ts}</a></td>
	</tr>
	{/section}
	</table>
	{/strip}
	{section name=rowadder start=1 loop=4}
	{assign var=index value=$smarty.section.rowadder.index}
	<div id="showrow{$index}">
            <span>
		{assign var=j value=$index+1}
	        <a onclick="show('optrow{$j}'); hide('showrow{$index}');return false;")">{ts}Add another row{/ts}</a>
	    </span>
	</div>
	{/section}
    </div>
*}
    <div class="form-item">
	<a onclick="document.getElementById('showoption').style.display='none'">{ts}close{/ts}</a> 
    </div>

</fieldset>


