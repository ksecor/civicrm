{* Report form criteria section *}
    {if $colGroups}
        <table style="width:98%;background-color:gainsboro;border:0;margin:0;">
            <tr>
	        <td><strong>{ts}Display Columns{/ts}</strong></td>
	    </tr>
	</table>
        {foreach from=$colGroups item=grpFields key=dnc}
            {assign  var="count" value="1"}
            <table class="form-layout">
                <tr>
                    {foreach from=$grpFields item=field key=title}
                        <td width="25%">{$form.fields.$field.html}</td>
                        {if $count is div by 4}
                            </tr>
                            <tr>
                        {/if}
                        {assign var="count" value=`$count+1`}
                    {/foreach}
                </tr>
            </table>
        {/foreach}
    {/if}
    
    {if $groupByElements}
        <br/>
        <table style="width:98%;background-color:gainsboro;border:0;margin:0;">
            <tr>
	        <td><strong>{ts}Group by Columns{/ts}</strong></td>
	    </tr>
	</table>
        {assign  var="count" value="1"}
        <table class="form-layout">
            <tr>
                {foreach from=$groupByElements item=gbElem key=dnc}
                    <td width="25%">{$form.group_bys[$gbElem].html}
                        {if $form.group_bys_freq[$gbElem].html}
                            ,&nbsp;&nbsp;{$form.group_bys_freq[$gbElem].label}&nbsp;{$form.group_bys_freq[$gbElem].html}
                        {/if}</td>
                        {if $count is div by 4}
                            </tr>
                            <tr>
                        {/if}
                        {assign var="count" value=`$count+1`}
                {/foreach}
            </tr>
        </table>      
    {/if}

    {if $form.options.html}
        <br/>
        <table style="width:98%;background-color:gainsboro;border:0;margin:0;">
            <tr>
	        <td><strong>{ts}Other Options{/ts}</strong></td>
	    </tr>
	</table>

        <table class="form-layout">
            <tr><td width="25%">{$form.options.html}</td></tr>
        </table>
    {/if}
  
        <br/>
        <table style="width:98%;background-color:gainsboro;border:0;margin:0;">
            <tr>
	        <td><strong>{ts}Set Filters{/ts}</strong></td>
	    </tr>
	</table>
        <table class="form-layout">
            {foreach from=$filters     item=table key=tableName}
                {foreach from=$table       item=field key=fieldName}
                    {assign var=fieldOp     value=$fieldName|cat:"_op"}
                    {assign var=filterVal   value=$fieldName|cat:"_value"}
                    {assign var=filterMin   value=$fieldName|cat:"_min"}
                    {assign var=filterMax   value=$fieldName|cat:"_max"}
                    {if $field.type & 4}
                        <tr>
                            <td style="vertical-align: top;"><strong>{$field.title}</strong></td>
                            <td colspan=2>{include file="CRM/Core/DateRange.tpl" fieldName=$fieldName}</td>
                        </tr>
	            {elseif $field.type == 17}                                
                        <tr>                                    
                            <td style="vertical-align: top;"><strong>{$field.title}</strong></td>
                            <td id="{$filterVal}_cell">{$form.$filterVal.html}</td>    				    
                        </tr>
                    {else}
                        <tr>
                            <td width="20%"><strong>{$field.title}</strong></td>
                            <td width="20%">{$form.$fieldOp.html}</td>
                            <td id="{$filterVal}_cell">{$form.$filterVal.label}&nbsp;{$form.$filterVal.html}</td>
                            <td id="{$filterMin}_max_cell">&nbsp;&nbsp;&nbsp;{$form.$filterMin.label}&nbsp;{$form.$filterMin.html}&nbsp;&nbsp;{$form.$filterMax.label}&nbsp;{$form.$filterMax.html}</td>
                        </tr>
                    {/if}
                {/foreach}
            {/foreach}
        </table>
 
    {if $form.charts.html}
        <br/>
        <table style="width:98%;background-color:gainsboro;border:0;margin:0;">
            <tr>
	        <td><strong>{ts}Chart Options{/ts}</strong></td>
	    </tr>
	</table>
        <table class="form-layout">
            <tr>
                <td>{$form.charts.label}&nbsp;&nbsp;{$form.charts.html}</td>
            </tr>
        </table>
    {/if}

    {literal}
    <script type="text/javascript">
    {/literal}
        {foreach from=$filters item=table key=tableName}
            {foreach from=$table item=field key=fieldName}
		{literal}var val = "dnc";{/literal}
		{if !($field.type == 4 OR $field.type == 17)} 
                    {literal}var val = document.getElementById("{/literal}{$fieldName}_op{literal}").value;{/literal}
		{/if}
                {literal}showHideMaxMinVal( "{/literal}{$fieldName}{literal}", val );{/literal}
            {/foreach}
        {/foreach}

        {literal}
        function showHideMaxMinVal( field, val ) {
            var fldVal    = field + "_value_cell";
            var fldMinMax = field + "_min_max_cell";
            if ( val == "bw" || val == "nbw" ) {
                cj('#' + fldVal ).hide();
                cj('#' + fldMinMax ).show();
            } else {
                cj('#' + fldVal ).show();
                cj('#' + fldMinMax ).hide();
            }
        }
    </script>
    {/literal}

    <br/><div>{$form.buttons.html}</div>
