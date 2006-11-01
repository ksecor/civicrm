    {if $validCiviContribute}
    <div id="contributeForm_show" class="data-group">
      <a href="#" onclick="hide('contributeForm_show'); show('contributeForm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
      <label>{ts}Contributions{/ts}</label>
    </div>
    <div id="contributeForm">
    <fieldset><legend><a href="#" onclick="hide('contributeForm'); show('contributeForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>{ts}Contributions{/ts}</legend>
    <table class="form-layout"> 
       {include file="CRM/Contribute/Form/Search/Common.tpl"}
    </table>
    </fieldset>
    </div>
    {/if}

    {if $validCiviMember}
    <div id="memberForm_show" class="data-group">
      <a href="#" onclick="hide('memberForm_show'); show('memberForm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
      <label>{ts}Memberships{/ts}</label>
    </div>
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

