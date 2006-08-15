{if ! empty( $row )} 
{* wrap in crm-container div so crm styles are used *}
<div id="crm-container" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">
<fieldset>
<table class="form-layout-compressed">                               
{foreach from=$row item=value key=name}
  <tr><td class="label">{$name}</td><td>{$value}</td></tr>
{/foreach}
</table>
</fieldset>
</div>
{/if} 
{* fields array is not empty *}
