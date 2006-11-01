{* Advanced Search Criteria Fieldset *}
<fieldset>
    <legend><span id="searchForm_hide"><a href="#" onclick="hide('searchForm','searchForm_hide'); show('searchForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a></span>
        {if $context EQ 'smog'}{ts}Find Members within this Group{/ts}
        {elseif $context EQ 'amtg'}{ts}Find Contacts to Add to this Group{/ts}
        {elseif $savedSearch}{ts 1=$savedSearch.name}%1 Smart Group Criteria{/ts}
        {else}{ts}Search Criteria{/ts}{/if}
    </legend>
    <div class="form-item">
    {strip}

{include file="CRM/Contact/Form/Search/Criteria/Basic.tpl"}
{include file="CRM/Contact/Form/Search/Criteria/Location.tpl"}
{include file="CRM/Contact/Form/Search/Criteria/ActivityHistory.tpl"}
{include file="CRM/Contact/Form/Search/Criteria/OpenActivity.tpl"}
{include file="CRM/Contact/Form/Search/Criteria/ChangeLog.tpl"}
{include file="CRM/Custom/Form/Search.tpl" showHideLinks=true}
{include file="CRM/Contact/Form/Search/Criteria/Contribute.tpl"}
{include file="CRM/Contact/Form/Search/Criteria/Quest.tpl"}
{include file="CRM/Contact/Form/Search/Criteria/Relationship.tpl"}
{include file="CRM/Contact/Form/Search/Criteria/Task.tpl"}

    <table class="form-layout">
    <tr>
    <td></td>
    <td class="label">{$form.buttons.html}</td>
    </tr>
    </table>
    {/strip}
    </div>
</fieldset>

<script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );

{if $customShow} 
    var showBlocks = new Array({$customShow});
    var hideBlocks = new Array({$customHide});	
    on_load_init_blocks( showBlocks, hideBlocks );
{/if}    
</script>
