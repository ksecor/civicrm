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
           {include file="CRM/Activity/Selector/Activity.tpl"}
         {/if}
       {/if}
   </td>
</tr>
</table>
