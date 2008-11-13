{* Base template for case activities like - Open Case, Change Case Type/Status ..*}
{if $caseAction}
    {include file="CRM/Case/Form/Activity/$caseAction.tpl"}
{/if}
