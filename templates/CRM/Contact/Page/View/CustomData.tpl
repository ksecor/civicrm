{* template for custom data *}

    {if $action eq 1 or $action eq 2}
        {include file="CRM/Contact/Form/CustomData.tpl"}
    {/if}

    {strip}
    {if $action eq 16 or $action eq 4} {* Browse or View actions *}
        {if $groupTree}
            <div class="form-item">
            
                {foreach from=$groupTree item=cd key=group_id}

                <div id="{$cd.title}[show]" class="data-group">
                <a href="#" onClick="hide('{$cd.title}[show]'); show('{$cd.title}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"></a><label>{ts}{$cd.title}{/ts}</label><br />
                </div>


                <div id="{$cd.title}">
                <fieldset><legend><a href="#" onClick="hide('{$cd.title}'); show('{$cd.title}[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"></a>{ts}{$cd.title}{/ts}</legend>
                    <dl>
                    {foreach from=$cd.fields item=cd_value key=field_id}
                    {assign var="name" value=`$cd_value.name`} 
                    {assign var="element_name value=$group_id|cat:_|cat:$field_id|cat:_|cat:$cd_value.name}
                    <dt>{$cd_value.label}</dt>
                    <dd>&nbsp;{$form.$element_name.html}</dd>
                    {/foreach}
                    </dl>
                </fieldset>
                </div>
                {/foreach}

                {if $editCustomData}
                <div class="action-link">
                {if $groupId}
                <a href="{crmURL p='civicrm/contact/view/cd/`$groupId` q="cid=`$contactId`&action=update&reset=1"}">&raquo; {ts 1=$groupTree.$groupId.title}Edit %1{/ts}</a>
                {else}
                <a href="{crmURL p='civicrm/contact/view/cd q="cid=`$contactId`&action=update&reset=1"}">&raquo; {ts}Edit custom data{/ts}</a>
                {/if}
                </div>
		{/if}
            </div>
        {else}
	    {if $editCustomData}
            <div class="messages status">
                {if $groupId}       
                <dl>
                    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
                    {capture assign=crmURL1}{crmURL p='civicrm/admin/custom/group' q="action=add&reset=1"}{/capture}
                    {capture assign=crmURL2}{crmURL p='civicrm/admin/custom/group/field' q="reset=1&action=add&gid=`$groupId`"}{/capture}
                    <dd>{ts 1=$crmURL1, 2=$crmURL2}There are either no Custom Groups or no Custom Fields entered for this Contact. You can either <a href="%1">add Custom Group</a> or <a href="%2">add Custom Field</a>.{/ts}</dd>
                </dl>    
                {else}    
                <dl>
                    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
                    {capture assign=crmURL}{crmURL p='civicrm/admin/custom/group' q="action=add&reset=1"}{/capture}
                    <dd>{ts 1=$crmURL}There are either no (Inline) Custom Groups or no Custom Fields entered for this Contact.{/ts}</dd>
                </dl>
                {/if}
            </div>
	    {/if}
        {/if}    
    {/if}
    {/strip}

<script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

    {* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
</script>
