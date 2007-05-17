{assign var="showBlocks" value="'searchForm'"}
{assign var="hideBlocks" value="'searchForm_show','searchForm_hide'"}

{include file="CRM/Contact/Form/Search/Zandigo/Form.tpl"}

{if $rows}
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlocks" value="'searchForm_show'"}
    {assign var="hideBlocks" value="'searchForm'"}
    {include file="CRM/Contact/Form/Search/Zandigo/Results.tpl"}
{/if}

{include file="CRM/Contact/Form/Search/Zandigo/js.tpl"}

{include file="CRM/common/showHide.tpl"}

