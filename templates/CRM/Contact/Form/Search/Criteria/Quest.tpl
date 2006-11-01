    {if $validQuest}
    <div id="questForm_show" class="data-group">
      <a href="#" onclick="hide('questForm_show'); show('questForm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
      <label>{ts}Quest Student{/ts}</label>
    </div>
    <div id="questForm">
    <fieldset><legend><a href="#" onclick="hide('questForm'); show('questForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a>{ts}Quest Student{/ts}</legend>
    <table class="form-layout"> 
       {include file="CRM/Quest/Form/Search/Common.tpl"}
    </table>
    </fieldset>
    </div>
    {/if}
        
