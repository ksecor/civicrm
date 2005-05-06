<div id="name" class="data-group form-item">
    <p>
	<label>{$displayName}</label>
    </p>
</div>

{if $customData}
  {foreach from=$customData item=cd}
    {$cd.label} {$cd.data} <br />   
  {/foreach}
{else}
   <div class="message status">
   <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
   There are no cd entered for this contact. You can <a href="{crmURL p='civicrm/contact/view/note' q='action=add'}">add one</a>.
   </div>
{/if}
