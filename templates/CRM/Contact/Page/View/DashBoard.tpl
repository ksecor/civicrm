<table class="no-border">
<tr>
    <td>
       {include file="CRM/Activity/Selector/Activity.tpl}
   </td>
   <td>
      <fieldset><legend>{ts}Quick Search{/ts}</legend>
      <form action="{$postURL}" method="post">
      <div class="form-item">
        {if $drupalFormToken}
            <input type="hidden" name="edit[token]" value="{$drupalFormToken}" />
        {/if}
        <input type="hidden" name="contact_type" value="" />
        <input type="text" name="sort_name" class="form-text required eight" value="" />
        <input type="submit" name="_qf_Basic_refresh" value="{ts}Search{/ts}" class="form-submit" />
        <br />
        <a href="{$advancedSearchURL}" title="{ts}Go to Advanced Search{/ts}">&raquo; {ts}Advanced Search{/ts}</a>
      </div>
      </form>
     </fieldset>
     <fieldset><legend>{ts}Menu{/ts}</legend>
       {$menuBlock.content}
     </fieldset>
     {if $shortcutBlock.content}
         <fieldset><legend>{ts}Shortcuts{/ts}</legend>
          {$shortcutBlock.content}
         </fieldset>
     {/if}
   </td>
</tr>
</table>
