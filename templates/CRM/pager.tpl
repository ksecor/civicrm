{if $pager and $pager->_response}

{if $pager->_response.numPages >= 1}

<table>
<tr>
<td>{$pager->_response.first}&nbsp;</td>
<td>{$pager->_response.back}&nbsp;</td>
<td>{$pager->_response.status}&nbsp;</td>
<td>{$pager->_response.next}&nbsp;</td>
<td>{$pager->_response.last}&nbsp;</td>
{if $location eq 'top'}
<td>{$pager->_response.titleTop}&nbsp;<input name="{$pager->_response.buttonTop}" value="Go!" type="submit"/></td>
{else}
<td>{$pager->_response.titleBottom}&nbsp;<input name="{$pager->_response.buttonBottom}" value="Go!" type="submit"/></td>
{/if}
</tr>
</table>

{/if} {* numPages > 1 *}

{/if} {* pager *}
