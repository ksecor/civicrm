{* Report form criteria section *}
    {if $colGroups}
        <table class="report-layout">
            <tr>
	           <th>Display Columns</th>
	    </tr>
	</table>
        {foreach from=$colGroups item=grpFields key=dnc}
            {assign  var="count" value="0"}
            <table class="report-layout">
                <tr>
                    {foreach from=$grpFields item=field key=title}
                        {assign var="count" value=`$count+1`}
                        <td width="25%">{$form.fields.$field.html}</td>
                        {if $count is div by 4}
                            </tr><tr>
                        {/if}
                    {/foreach}
                    {if $count is not div by 4}
                        <td colspan="4 - ($count % 4)"></td>
                    {/if}
                </tr>
            </table>
	    <hr style="height:2px;width:100%;background-color:#DCDCDC;margin:0;"/>
        {/foreach}
    {/if}
    
    {if $groupByElements}
        <br/>
        <table class="report-layout">
            <tr>
	          <th>Group by Columns</th>
	        </tr>
    	</table>
        {assign  var="count" value="0"}
        <table class="report-layout">
            <tr>
                {foreach from=$groupByElements item=gbElem key=dnc}
                    {assign var="count" value=`$count+1`}
                    <td width="25%" {if $form.fields.$gbElem} onClick="selectGroupByFields('{$gbElem}');"{/if}>
                        {$form.group_bys[$gbElem].html}
                        {if $form.group_bys_freq[$gbElem].html}:<br>
                            &nbsp;&nbsp;{$form.group_bys_freq[$gbElem].label}&nbsp;{$form.group_bys_freq[$gbElem].html}
                        {/if}
                    </td>
                    {if $count is div by 4}
                        </tr><tr>
                    {/if}
                {/foreach}
                {if $count is not div by 4}
                    <td colspan="4 - ($count % 4)"></td>
                {/if}
            </tr>
        </table>      
    {/if}

    {if $form.options.html || $form.options.html}
        <br/>
        <table class="report-layout">
            <tr>
	        <th>Other Options</th>
	    </tr>
	</table>

        <table class="report-layout">
            <tr>
	        <td>{$form.options.html}</td>
	        {if $form.blank_column_end}
	            <td>{$form.blank_column_end.label}&nbsp;&nbsp;{$form.blank_column_end.html}</td>
                {/if}
            </tr>
        </table>
    {/if}
  
        <br/>
        <table class="report-layout">
            <tr>
	        <th>Set Filters</th>
	    </tr>
	</table>
        <table class="report-layout">
            {foreach from=$filters     item=table key=tableName}
                {foreach from=$table       item=field key=fieldName}
                    {assign var=fieldOp     value=$fieldName|cat:"_op"}
                    {assign var=filterVal   value=$fieldName|cat:"_value"}
                    {assign var=filterMin   value=$fieldName|cat:"_min"}
                    {assign var=filterMax   value=$fieldName|cat:"_max"}
                    {if $field.operatorType & 4}
                        <tr class="report-contents">
                            <th class="report-contents">{$field.title}</td>
                            {include file="CRM/Core/DateRange.tpl" fieldName=$fieldName}
                        </tr>
                    {else}
                        <tr {if $field.no_display} style="display: none;"{/if}>
                            <th class="report-contents">{$field.title}</th>
                            <td class="report-contents">{$form.$fieldOp.html}</td>
                            <td>
                               <span id="{$filterVal}_cell">{$form.$filterVal.label}&nbsp;{$form.$filterVal.html}</span>
                               <span id="{$filterMin}_max_cell">{$form.$filterMin.label}&nbsp;{$form.$filterMin.html}&nbsp;&nbsp;{$form.$filterMax.label}&nbsp;{$form.$filterMax.html}</span>
                            </td>
                        </tr>
                    {/if}
                {/foreach}
            {/foreach}
        </table>
 
    {literal}
    <script type="text/javascript">
    {/literal}
        {foreach from=$filters item=table key=tableName}
            {foreach from=$table item=field key=fieldName}
		{literal}var val = "dnc";{/literal}
		{if !($field.operatorType == 4) && !$field.no_display} 
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
	    
	function selectGroupByFields(id) {
	    var field = 'fields\['+ id+'\]';
	    var group = 'group_bys\['+ id+'\]';	
	    var groups = document.getElementById( group ).checked;
	    if ( groups == 1 ) {
	        document.getElementById( field ).checked = true;	
	    } else {
	        document.getElementById( field ).checked = false;	    
	    }	
	}
    </script>
    {/literal}

    <br/><div>{$form.buttons.html}</div>