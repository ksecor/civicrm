{* this template is used for adding/editing tags  *}

<form {$form.attributes}>
<fieldset><legend>{ts}Tags{/ts}</legend>
    <p>
    {if $action eq 4}
        {ts 1=$displayName}Current tags for <strong>%1</strong> are highlighted. You can
        add or remove tags from{/ts} <a href="{crmURL p='civicrm/contact/view/tag' q='action=update'}">{ts}Edit Tags{/ts}</a>.
    {else}
        {ts}Mark or unmark the checkboxes, and click
        'Update Tags' to modify tags.{/ts}
    {/if}
    </p>
    
      {foreach from=$category item="row" key = "id"}
        <div class="form-item {if $action eq 4 AND $form.categoryList[$id].value}label{/if}">
         {ts}{$form.categoryList[$id].html} &nbsp;{$row}{/ts}
        </div>
      {/foreach}

    {* Show Edit Tags link if in View mode *}
    {if $action eq 16}
        </fieldset>
        <div class="action-link">
          <a href="{crmURL p='civicrm/contact/view/tag' q='action=update'}">&raquo; Edit Tags</a>
        </div>
    {else}
       <div class="form-item">{$form.buttons.html}</div>
       </fieldset>
    {/if}

 	
</form>

