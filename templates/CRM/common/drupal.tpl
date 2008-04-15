{if $config->debug}
{include file="CRM/common/debug.tpl"}
{/if}

<div id="crm-container" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">
<script type="text/javascript" src="{$config->resourceBase}js/Common.js"></script>
{include file="CRM/common/dojo.tpl"}

{if $displayRecent and $recentlyViewed}
    {include file="CRM/common/recentlyViewed.tpl"}
{/if}

{if ! $hidePrinterIcon}
{* Printer friendly link/icon. *}
<div id="printer-friendly"><a href="{$printerFriendly}" title="{ts}Printer-friendly view of this page.{/ts}"><img src="{$config->resourceBase}i/print_preview.gif" alt="{ts}Printer-friendly view of this page.{/ts}" /></a></div>
{/if}

<div class="spacer"></div>

{if $localTasks}
    {include file="CRM/common/localNav.tpl"}
{/if}

{include file="CRM/common/status.tpl"}

<!-- .tpl file invoked: {$tplFile}. Call via form.tpl if we have a form in the page. -->
{if $isForm}
    {include file="CRM/Form/$formTpl.tpl"}
{else}
    {include file=$tplFile}
{/if}

{include file="CRM/common/footer.tpl"}
</div> {* end crm-container div *}
