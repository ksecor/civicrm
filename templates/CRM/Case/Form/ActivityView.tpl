{* View Case Activities *}

<table class="report">
{foreach from=$report.fields item=row name=report}
<tr>
    <td class="label">{$row.label}</td>
    {if $smarty.foreach.report.first AND ( $revisionURL OR $parentURL )} {* Add a cell to first row with links to prior revision listing and Prompted by (parent) as appropriate *}
        <td>{$row.value}</td>
        <td style="padding-right: 50px; text-align: right;">
            {if $revisionURL}<a href="{$revisionURL}">&raquo; {ts}Prior revisions{/ts}</a><br />{/if}
            {if $parentURL}<a href="$parentURL">&raquo; {ts}Prompted by{/ts}</a>{/if}
        </td>
    {else}
        <td colspan="2">{$row.value}</td>
    {/if}
</tr>
{/foreach}
</table>