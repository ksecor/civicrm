<div id="searchForm">
<fieldset><legend>{ts}Select Columns{/ts}</legend>
   {foreach from=$colGroups item=grp key=dnc}
   {if $dnc neq 0}<br/>{/if}
   <table class="form-layout"><tr><td width="25%">{$form.select_columns[$grp].html}</td></tr></table>
   {/foreach}
</fieldset>

<fieldset><legend>{ts}Options{/ts}</legend>
   <table class="form-layout">
      <tr><td>{$form.options.html}</td></tr>
   </table>
</fieldset>

<fieldset><legend>{ts}Select Filters{/ts}</legend>
   <table class="form-layout">
   {foreach from=$filters     item=table key=tableName}
   {foreach from=$table       item=field key=fieldName}
      {assign var=fieldOp     value=$fieldName|cat:"_op"}
      {assign var=filterVal   value=$fieldName|cat:"_value"}
      {assign var=filterMin   value=$fieldName|cat:"_min"}
      {assign var=filterMax   value=$fieldName|cat:"_max"}
      {if $field.type eq 12}
         <tr><td style="vertical-align: top;">{$field.title}</td><td colspan=2>{include file="CRM/Core/DateRange.tpl"}</td></tr>
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
	
<div id="crm-submit-buttons">{$form.buttons.html}</div>
</div>

{literal}
<script type="text/javascript">
   cj(function() {
      cj("[type=checkbox]:checked").attr('disabled', true);
   });

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

{if $rows}
<br/>
<fieldset><legend>{ts}Result Set{/ts}</legend>
   <table class="form-layout">
      <tr class="columnheader">
      {foreach from=$columnHeaders item=header key=field}
         <th>{$header}</th>
      {/foreach}
      </tr>

      {foreach from=$rows item=row}
      <tr class="{cycle values="odd-row,even-row"}">
         {foreach from=$columnHeaders item=header key=field}
            <td>{$row.$field}</td>
         {/foreach}
      </tr>
      {/foreach}
   </table>
</fieldset>
{/if}