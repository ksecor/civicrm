{if $pager}

{if $pager.numPages >= 1}

<table>
<tr>
<td>{$pager.first}&nbsp;</td>
<td>{$pager.back}&nbsp;</td>
<td>{$pager.status}&nbsp;</td>
<td>{$pager.next}&nbsp;</td>
<td>{$pager.last}&nbsp;</td>
{if $location eq 'top'}
<td>{$pager.titleTop}&nbsp;<input name="{$pager.buttonTop}" value="Go!" type="submit"/></td>
{else}
<td>{$pager.titleBottom}&nbsp;<input name="{$pager.buttonBottom}" value="Go!" type="submit"/></td>
{/if}
</tr>
</table>

{/if} {* numPages > 1 *}

{/if} {* pager *}
