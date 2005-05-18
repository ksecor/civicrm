{* template for custom data *}

    {if $action eq 1 or $action eq 2}
        {include file="CRM/Contact/Form/CustomData.tpl"}
    {/if}

    {strip}
    {if $action eq 16 or $action eq 4}
    <p>
    <div id="custom-data" class="label">Existing Custom Groups</div>   

    <div class="form-item">
    {foreach from=$groupTree item=cd key=group_id}
    <fieldset><legend>{$cd.title}</legend>
        {foreach from=$cd.fields item=cd_value key=field_id}
        {assign var="name" value=`$cd_value.name`} 
        {assign var="element_name value=$group_id|cat:_|cat:$field_id|cat:_|cat:$cd_value.name}
        <dl>
        <dt>{$cd_value.label}</dt>
        <dd>{$form.$element_name.html}</dd>
        </dl>
        {/foreach}
    </fieldset>
    {/foreach}
    
    <div class="action-link">
    <a href="{crmURL p='civicrm/contact/view/cd' q="cid=`$contactId`&action=update"}">&raquo; Edit custom data</a>
    </div>
    </div>
    </p>

    {/if}
    {/strip}


