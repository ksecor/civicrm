{if $displayRecent and $recentlyViewed}
    {include file="CRM/common/recentlyViewed.tpl"}
{/if}

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
