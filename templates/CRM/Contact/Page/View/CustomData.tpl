{* template for custom data *}

    {if $action eq 1 or $action eq 2}
        {include file="CRM/Contact/Form/CustomData.tpl"}
    {/if}

    {strip}
    {if $action eq 16 or $action eq 4} {* Browse or View actions *}
        {if $groupTree}
            <div class="form-item">
                {foreach from=$groupTree item=cd key=group_id}
                <fieldset><legend>{$cd.title}</legend>
                    <dl>
                    {foreach from=$cd.fields item=cd_value key=field_id}
                    {assign var="name" value=`$cd_value.name`} 
                    {assign var="element_name value=$group_id|cat:_|cat:$field_id|cat:_|cat:$cd_value.name}
                    <dt>{$cd_value.label}</dt>
                    <dd>&nbsp;{$form.$element_name.html}</dd>
                    {/foreach}
                    </dl>
                </fieldset>
                {/foreach}
                
                <div class="action-link">
                <a href="{crmURL p='civicrm/contact/view/cd' q="cid=`$contactId`&action=update"}">&raquo; {ts}Edit custom data{/ts}</a>
                </div>
            </div>
        {else}
            <div class="message status">
                <dl>
                    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
                    <dd>{ts}Custom Data fields are not currently used for this type of contact.{/ts}</dd>
                </dl>
            </div>
        {/if}    
    {/if}
    {/strip}
