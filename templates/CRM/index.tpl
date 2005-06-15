{if $smarty.get.smartyDebug}
{debug}
{/if}
{if $smarty.get.sessionReset}
{$session->reset()}
{/if}
{if $smarty.get.sessionDebug}
{$session->debug($smarty.get.sessionDebug)}
{/if}

<div id="crm-container">
<script type="text/javascript" src="{$config->resourceBase}js/Common.js"></script>

{if $recentlyViewed}
    {include file="CRM/common/recentlyViewed.tpl"}
{/if}

{if $localTasks}
    {include file="CRM/common/localNav.tpl"}
{/if}

{if $config->userFramework eq 'Mambo'}
{include file="CRM/common/mambo.tpl"}
{else}
{include file="CRM/common/drupal.tpl"}
{/if}
</div> {* end crm-container div *}
