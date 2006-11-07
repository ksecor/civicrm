{if $validCiviMember}
    <div id="memberForm">
    <fieldset><legend><a href="#" onclick="hide('memberForm'); show('memberForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>{ts}Memberships{/ts}</legend>
    <table class="form-layout">
       <tr>
         <td> 
          {include file="CRM/Member/Form/Search/Common.tpl"}
         </td> 
       </tr>
    </table>
    </fieldset>
    </div>
{/if}
