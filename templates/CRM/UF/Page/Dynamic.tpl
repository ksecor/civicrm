{if ! empty( $row )}
<div id="crm-container">
<fieldset>
<table class="form-layout-compressed">                               
{foreach from=$row item=value key=name}
  <tr><td class="label">{$name}</td><td>{$value}</td></tr>
{/foreach}
</table>
</fieldset>
</div>
{/if} {* fields array is not empty *}