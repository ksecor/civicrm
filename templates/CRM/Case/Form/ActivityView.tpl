{* View Case Activities *}
<table class="report">
{foreach from=$report.fields item=row}
<tr><td class="label">{$row.label}</td><td>{$row.value}</td></tr>
{/foreach}
</table>