{if $config->debug}
{include file="CRM/common/debug.tpl"}
{/if}

<div id="crm-container" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">
{if $config->customCSSURL}
    <link rel="stylesheet" href="{$config->customCSSURL}" type="text/css" />
{else}
    {assign var="revamp" value=0}
    {foreach from=$config->revampPages item=page}
        {if $page eq $tplFile}
            {assign var="revamp" value=1}
        {/if}
    {/foreach}
    
    {if $revamp eq 0}
        <link rel="stylesheet" href="{$config->resourceBase}css/civicrm.css" type="text/css" />
    {else}
        <link rel="stylesheet" href="{$config->resourceBase}css/civicrm-new.css" type="text/css" />
    {/if}
{/if}
{*include file="CRM/common/jquery.tpl"*}
{include file="CRM/common/dojo.tpl"}
{include file="CRM/common/action.tpl"}
{if $buildNavigation }
    {include file="CRM/common/Navigation.tpl" }
{/if}
<script type="text/javascript" src="{$config->resourceBase}js/Common.js"></script>

{if isset($browserPrint) and $browserPrint}
{* Javascript window.print link. Used for public pages where we can't do printer-friendly view. *}
<div id="printer-friendly"><a href="javascript:window.print()" title="{ts}Print this page.{/ts}"><img src="{$config->resourceBase}i/print_preview.gif" alt="{ts}Print this page.{/ts}" /></a></div>
{else}
{* Printer friendly link/icon. *}
<div id="printer-friendly">
<a href="{$printerFriendly}" title="{ts}Printer-friendly view of this page.{/ts}"><img src="{$config->resourceBase}i/print_preview.gif" alt="{ts}Printer-friendly view of this page.{/ts}" /></a>
</div>
{/if}

{*{include file="CRM/common/langSwitch.tpl"}*}

<div class="spacer"></div>

{if isset($localTasks) and $localTasks}
    {include file="CRM/common/localNav.tpl"}
{/if}

{include file="CRM/common/status.tpl"}

<!-- .tpl file invoked: {$tplFile}. Call via form.tpl if we have a form in the page. -->
{if isset($isForm) and $isForm}
    {include file="CRM/Form/$formTpl.tpl"}
{else}
    {include file=$tplFile}
{/if}

{if ! $urlIsPublic}
{include file="CRM/common/footer.tpl"}
{/if}

{* We need to set jquery $ object back to $*}
<script type="text/javascript">jQuery.noConflict(true);</script>
</div> {* end crm-container div *}
