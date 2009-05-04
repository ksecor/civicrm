<table class="no-border">
<tr>
   <td>
       {if empty($hookContent)}
         {include file="CRM/Activity/Selector/Activity.tpl}
       {else}
         {if $hookContentPlacement != 2 && $hookContentPlacement != 3}
           {include file="CRM/Activity/Selector/Activity.tpl}
         {/if}

         {foreach from=$hookContent key=title item=content}
           <fieldset><legend>{$title}</legend>
             {$content}
           </fieldset>
         {/foreach}

         {if $hookContentPlacement == 2}
           {include file="CRM/Activity/Selector/Activity.tpl}
         {/if}
       {/if}
   </td>
   <td>
      <fieldset><legend>{ts}Quick Search{/ts}</legend>
      {capture assign="quickSearchURL"}{crmURL p='civicrm/contact/search' q='reset=1'}{/capture}
      <form action="{$quickSearchURL}" method="post">
      <div class="form-item">
        {if isset($drupalFormToken) and $drupalFormToken}
            <input type="hidden" name="edit[token]" value="{$drupalFormToken}" />
        {/if}
        <input type="hidden" name="contact_type" value="" />
        <input type="text" name="sort_name" class="form-text required eight" value="" />
        <input type="submit" name="_qf_Basic_refresh" value="{ts}Search{/ts}" class="form-submit" />
        <br />
        {capture assign="advancedSearchURL"}{crmURL p='civicrm/contact/search/advanced' q='reset=1'}{/capture}
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
