{if ! empty( $row )}
<div id="crm-container">
<fieldset>
{if $help_pre}<div class="messages help">{$help_pre}</div>{/if}
<table class="form-layout-compressed">                               
{foreach from=$row item=value key=name}
  <tr><td class="label">{$name}</td><td>{$value}</td></tr>
{/foreach}
</table>
{if $help_post}<div class="messages help">{$help_post}</div>{/if}
</fieldset>
</div>
{/if} {* fields array is not empty *}
