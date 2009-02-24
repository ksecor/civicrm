{if ! empty( $row )} 
{* wrap in crm-container div so crm styles are used *}
<div id="crm-container" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">
<fieldset>
<table class="form-layout-compressed">                               
{foreach from=$row item=value key=rowName name=profile}
  <tr id="row-{$smarty.foreach.profile.iteration}"><td class="label">{$rowName}</td><td class="view-value">{$value}</td></tr>
{/foreach}
</table>
</fieldset>
</div>
{/if} 
{* fields array is not empty *}