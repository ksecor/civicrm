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
      <dt>{$label}</dt><dd>{$form.$field.html}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$form.$filterValue.label}:&nbsp;{$form.$filterValue.html}&nbsp;{$form.$filterMin.label}:&nbsp;{$form.$filterMin.html}&nbsp;{$form.$filterMax.label}:&nbsp;{$form.$filterMax.html}</dd>
   {/foreach}
</dl>
</fieldset>

<div id="crm-submit-buttons">{$form.buttons.html}</div>

</div>
