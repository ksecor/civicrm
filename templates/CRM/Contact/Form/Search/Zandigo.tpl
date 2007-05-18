{assign var="showBlocks" value="'searchForm'"}
{assign var="hideBlocks" value="'searchForm_show'"}
{assign var="zIconURL" value=$config->userFrameworkBaseURL|cat:"themes/zandigo/images/icons/"}

{include file="CRM/Contact/Form/Search/Zandigo/Form.tpl"}

{if $rowsEmpty}
    {* Search request has returned 0 more matching rows. Display empty results, and collapse the search criteria fieldset. *}
    {assign var="showBlocks" value="'searchForm_show'"}
    {assign var="hideBlocks" value="'searchForm'"}
    {include file="CRM/Contact/Form/Search/Zandigo/EmptyResults.tpl"}
{/if}

{if $rows}
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlocks" value="'searchForm_show'"}
    {assign var="hideBlocks" value="'searchForm'"}
    {include file="CRM/Contact/Form/Search/Zandigo/Results.tpl"}
{/if}

{include file="CRM/Contact/Form/Search/Zandigo/js.tpl"}

{include file="CRM/common/showHide.tpl"}

