<table class="no-border">
<tr>
    <td>
       {include file="CRM/Activity/Selector/Activity.tpl}
   </td>
   <td>
     <fieldset><legend>{ts}Choose Language{/ts}</legend>
       <a href="{$smarty.server.REQUEST_URI}&lcMessages=en_US">English</a>,
       <a href="{$smarty.server.REQUEST_URI}&lcMessages=nl_NL">Dutch</a>,
       <a href="{$smarty.server.REQUEST_URI}&lcMessages=it_IT">Italian</a>,
       <a href="{$smarty.server.REQUEST_URI}&lcMessages=pt_BR">Brazilian Portuguese</a>,
       <a href="{$smarty.server.REQUEST_URI}&lcMessages=es_ES">Spanish</a>,
       <a href="{$smarty.server.REQUEST_URI}&lcMessages=de_DE">German</a>,
       <a href="{$smarty.server.REQUEST_URI}&lcMessages=fr_CA">Canadian French</a>,
       <a href="{$smarty.server.REQUEST_URI}&lcMessages=tr_TR">Turkish</a>,
       <a href="{$smarty.server.REQUEST_URI}&lcMessages=pl_PL">Polish</a>,
       <a href="{$smarty.server.REQUEST_URI}&lcMessages=pt_PT">Portuguese</a>
     </fieldset>
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
