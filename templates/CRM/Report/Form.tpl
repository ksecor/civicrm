{if ! $printOnly}
<div id="searchForm">
<fieldset><legend>{ts}Display Columns{/ts}</legend>
   {foreach from=$colGroups item=grp key=dnc}
   {if $dnc neq 0}<br/>{/if}
   <table class="form-layout"><tr><td width="25%">{$form.select_columns[$grp].html}</td></tr></table>
   {/foreach}
</fieldset>

<fieldset><legend>{ts}Group by Columns{/ts}</legend>
   <table class="form-layout"><tr>
   {foreach from=$groupByElements item=gbElem key=dnc}
      <td width="25%">{$form.group_bys[$gbElem].html}{if $form.group_bys_freq[$gbElem].html},&nbsp;&nbsp;{$form.group_bys_freq[$gbElem].label}&nbsp;{$form.group_bys_freq[$gbElem].html}{/if}</td>
   {/foreach}
   </table></tr>
</fieldset>

{if $form.options.html}
<fieldset><legend>{ts}Other Options{/ts}</legend>
   <table class="form-layout">
      <tr><td width="25%">{$form.options.html}</td></tr>
   </table>
</fieldset>
{/if}

<fieldset><legend>{ts}Set Filters{/ts}</legend>
   <table class="form-layout">
   {foreach from=$filters     item=table key=tableName}
   {foreach from=$table       item=field key=fieldName}
      {assign var=fieldOp     value=$fieldName|cat:"_op"}
      {assign var=filterVal   value=$fieldName|cat:"_value"}
      {assign var=filterMin   value=$fieldName|cat:"_min"}
      {assign var=filterMax   value=$fieldName|cat:"_max"}
      {if $field.type eq 12}
         <tr><td style="vertical-align: top;">{$field.title}</td><td colspan=2>{include file="CRM/Core/DateRange.tpl" fieldName=$fieldName}</td></tr>
      {else}
         <tr><td width="20%">{$field.title}</td>
             <td width="20%">{$form.$fieldOp.html}</td>
             <td id="{$filterVal}_cell">{$form.$filterVal.label}:&nbsp;{$form.$filterVal.html}</td>
             <td id="{$filterMin}_max_cell">&nbsp;&nbsp;&nbsp;{$form.$filterMin.label}:&nbsp;{$form.$filterMin.html}&nbsp;&nbsp;{$form.$filterMax.label}:&nbsp;{$form.$filterMax.html}</td>
         </tr>
      {/if}
   {/foreach}
   {/foreach}
   </table>
</fieldset>

{if $instanceForm}
  {include file="CRM/Report/Form/Instance.tpl"}
{/if}
	
<div id="crm-submit-buttons">{$form.buttons.html}</div>

{literal}
<script type="text/javascript">
   {/literal}
   {foreach from=$filters     item=table key=tableName}
   {foreach from=$table       item=field key=fieldName}
      {literal}showHideMaxMinVal( "{/literal}{$fieldName}{literal}", "dnc" );{/literal}
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

{/if}

{if $rows}
<br/>
   {if $statistics}
   <table class="report">
   <tr class="columnheader-dark"><th colspan=2>Statistics</th></tr>
   {foreach from=$statistics item=stats key=statName}
      <tr><td><strong>{$stats.title}</strong></td><td>{$stats.value}</td></tr>
   {/foreach}
   </table>
   {/if}

   <table class="form-layout">
      <tr class="columnheader">
      {foreach from=$columnHeaders item=header key=field}
         <th>{$header.title}</th>
      {/foreach}
      </tr>

      {foreach from=$rows item=row}
      <tr class="{cycle values="odd-row,even-row"}">
         {foreach from=$columnHeaders item=header key=field}
            <td>{if $header.type eq 'Date'}
	    	    {$row.$field|truncate:10:''|crmDate}
		{elseif $header.type eq 'Money'}
	    	    {$row.$field|crmMoney}
	        {else}
	    	    {$row.$field}
		{/if}
	    </td>
         {/foreach}
      </tr>
      {/foreach}

      {foreach from=$grandStat item=row}
      <tr>
         {foreach from=$columnHeaders item=header key=field}
            <td><strong>
		{if $header.type eq 'Date'}
	    	    {$row.$field|truncate:10:''|crmDate}
		{elseif $header.type eq 'Money'}
	    	    {$row.$field|crmMoney}
	        {else}
	    	    {$row.$field}
		{/if}
		</strong></td>
         {/foreach}
      </tr>
      {/foreach}
   </table>
{/if}
</div>
