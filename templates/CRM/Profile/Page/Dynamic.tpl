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

{literal}
<script type="text/javascript">
function popUp (path) 
{
window.open(path,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,screenX=150,screenY=150,top=150,left=150')
}
</script>
{/literal}