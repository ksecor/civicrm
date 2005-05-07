<div id="name" class="data-group form-item">
    <p>
	<label>{$displayName}</label>
    </p>
</div>

<div class="form-item">
{if $customData}
  {foreach from=$customData item=cd}
    <dl><dt>{$cd.label}</dt>
            <dd>{$cd.data}</dd></dl>   
  {/foreach}
{else}
   <div class="message status">
   <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
   There are no cd entered for this contact. You can <a href="{crmURL p='civicrm/contact/view/note' q='action=add'}">add one</a>.
   </div>
{/if}
</div>