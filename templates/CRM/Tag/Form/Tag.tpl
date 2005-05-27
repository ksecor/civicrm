{* this template is used for adding/editing tags  *}

<fieldset><legend>{ts}Tags{/ts}</legend>
    <p>
    {if $action eq 16}
        {ts 1=$displayName}Current tags for <strong>%1</strong> are highlighted. You can
        add or remove tags from{/ts} <a href="{crmURL p='civicrm/contact/view/tag' q='action=update'}">{ts}Edit Tags{/ts}</a>.
    {else}
        {ts}Mark or unmark the checkboxes, and click
        'Update Tags' to modify tags.{/ts}
    {/if}
    </p>
    
      {foreach from=$tag item="row" key = "id"}

        <div class="form-item" id ="rowid{$id}">

         {ts}{$form.tagList[$id].html} &nbsp;{$row}{/ts} 

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

 <script type="text/javascript">
 {* this function is called to change the color of selected row(s) *}
    var fname = "{$form.formName}";	
    on_load_init_check(fname);
 </script>
