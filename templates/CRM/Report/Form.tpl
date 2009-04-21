<div class="form-layout">

<fieldset><legend>{ts}Select Columns{/ts}</legend>
    {$form.select_columns.html}
</fieldset>

<fieldset><legend>{ts}Select Filters{/ts}</legend>
<dl>
   {foreach from=$filterFields item=filterField key=label}
      {assign var=field value=$filterField|cat:"_operation"}
      {assign var=filterValue value=$filterField|cat:"_operation_value"}
      {assign var=filterMin value=$filterField|cat:"_operation_min"}
      {assign var=filterMax value=$filterField|cat:"_operation_max"}
      <dt>{$label}</dt>
      <dd><table class="form-layout">
            <tr><td width="30%">{$form.$field.html}</td>
                <td id="{$filterValue}_cell">{$form.$filterValue.label}:&nbsp;{$form.$filterValue.html}</td>
                <td id="{$filterMin}_max_cell">{$form.$filterMin.label}:&nbsp;{$form.$filterMin.html}&nbsp;{$form.$filterMax.label}:&nbsp;{$form.$filterMax.html}</td>
            </tr>
          </table>
      </dd>
   {/foreach}
</dl>
</fieldset>
	
<fieldset><legend>{ts}Sub-total Columns{/ts}</legend>
    {include file="CRM/Core/DateRange.tpl"}
</fieldset>

<div id="crm-submit-buttons">{$form.buttons.html}</div>

</div>

{literal}
<script type="text/javascript">
   {/literal}
   {foreach from=$filterFields item=filterField key=label}
      {literal}showHideMaxMinVal( "{/literal}{$filterField}{literal}", "dnc" );{/literal}
   {/foreach}
   {literal}

   function showHideMaxMinVal( field, val ) {
      var fldVal    = field + "_operation_value_cell";
      var fldMinMax = field + "_operation_min_max_cell";
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