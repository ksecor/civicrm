{* this template is used for building tabbed custom data *} 
{if $cdType }
    {include file="CRM/Custom/Form/CustomData.tpl"}
{else}
    <div id="customData"></div>
    <div class="html-adjust">{$form.buttons.html}</div>  

    {*include custom data js file*}
    {include file="CRM/common/customData.tpl"}
{/if}

