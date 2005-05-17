{* template for custom data *}
{if $contactType eq 'Individual'}
    {if $action eq 1 or $action eq 2}
        {include file="CRM/Contact/Form/CustomData.tpl"}
    {/if}

    {strip}
    {if $action eq 16 or $action eq 4}
    <p>
    <div id="custom-data" class="label">Existing Custom Groups</div>   

    <div class="form-item">
    {foreach from=$groupTree item=cd_view}
    <fieldset><legend>{$cd_view.title}</legend>
        {foreach from=$cd_view.fields item=cd_value_view}
        <dl>
        <dt>{$cd_value_view.label}</dt>
        <dd>{if $cd_value_view.customValue}
            {if $cd_value_view.html_type eq "Radio"} 
            {if $cd_value_view.customValue.data eq 1} Yes {else} No {/if}
            {else}
                {$cd_value_view.customValue.data}
            {/if} {* html_type *}
            {else}
            --
            {/if} {* customValue *}
        </dd>
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
{else}
    <div class="mesage status">
    <dl>
    <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
    <dd>There are no Custom Groups for <span class="label">{$contactType}</span>.</dd>
    </dl>
    </div>
{/if}

