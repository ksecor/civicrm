{if $config->debug}
{include file="CRM/common/debug.tpl"}
{/if}

<div id="crm-container" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">
<script type="text/javascript" src="{$config->resourceBase}js/Common.js"></script>
{include file="CRM/common/dojo.tpl"}

{if $displayRecent and $recentlyViewed}
    {include file="CRM/common/recentlyViewed.tpl"}
{/if}

{if isset($browserPrint) and $browserPrint}
{* Javascript window.print link. Used for public pages where we can't do printer-friendly view. *}
<div id="printer-friendly"><a href="javascript:window.print()" title="{ts}Print this page.{/ts}"><img src="{$config->resourceBase}i/print_preview.gif" alt="{ts}Print this page.{/ts}" /></a></div>
{else}
{* Printer friendly link/icon. *}
<div id="printer-friendly">
<a href="{$printerFriendly}" title="{ts}Printer-friendly view of this page.{/ts}"><img src="{$config->resourceBase}i/print_preview.gif" alt="{ts}Printer-friendly view of this page.{/ts}" /></a>
</div>
{/if}

{include file="CRM/common/langSwitch.tpl"}

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

</div> {* end crm-container div *}
