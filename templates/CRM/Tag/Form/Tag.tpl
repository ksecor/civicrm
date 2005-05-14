{* this template is used for adding/editing tags  *}

<form {$form.attributes}>
<fieldset><legend>{t}Tags{/t}</legend>
    <p>
    {if $action eq 4}
        {t}Current tags for <strong>{$displayName}</strong> are highlighted. You can
        add or remove tags from <a href="{crmURL p='civicrm/contact/view/tag' q='action=update'}">Edit Tags</a>.{/t}
    {else}
        {t}Mark or unmark the checkboxes, and click
        'Update Tags' to modify tags.{/t}
    {/if}
    </p>
    
      {foreach from=$category item="row" key = "id"}
        <div class="form-item {if $action eq 4 AND $form.categoryList[$id].value}label{/if}">
         {t}{$form.categoryList[$id].html} &nbsp;{$row}{/t}
        </div>
      {/foreach}

    {* Show Edit Tags link if in View mode *}
    {if $action eq 4}
        <div class="action-link">
          <a href="{crmURL p='civicrm/contact/view/tag' q='action=update'}">&raquo; Edit Tags</a>
        </div>
    {else}
       <div class="form-item">{$form.buttons.html}</div>
    {/if}
</fieldset>

 	
</form>

