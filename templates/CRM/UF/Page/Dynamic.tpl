{if ! empty( $row )}
<div class="form-item">                               
<fieldset> 
<dl>
{foreach from=$row item=value key=name}
  <dt>{$name}</dt><dd>{$value}</dd>
{/foreach}
</dl>
</fieldset>
</div> {* end crm-container div *}
{/if} {* fields array is not empty *}