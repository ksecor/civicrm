{if $pager}

{if $pager.numPages >= 1}

<table>
<tr>
<td>{$pager.first}&nbsp;</td>
<td>{$pager.back}&nbsp;</td>
<td>{$pager.next}&nbsp;</td>
<td>{$pager.last}&nbsp;</td>
{if $location eq 'top'}
<td>{$pager.titleTop}&nbsp;</td>
{else}
<td>{$pager.titleBottom}&nbsp;</td>
{/if}
</tr>
</table>

{/if} {* numPages > 1 *}

{/if} {* pager *}
