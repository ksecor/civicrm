{if $pager and $pager->_response}

{if $pager->_response.numPages >= 1}

<div>
<span>{$pager->_response.first}&nbsp;
{$pager->_response.back}&nbsp;
{$pager->_response.status}&nbsp;
{$pager->_response.next}&nbsp;
{$pager->_response.last}&nbsp;</span>
{if $location eq 'top'}
<span>{$pager->_response.titleTop}&nbsp;<input name="{$pager->_response.buttonTop}" value="Go!" type="submit"/></span>
{else}
<span>{$pager->_response.titleBottom}&nbsp;<input name="{$pager->_response.buttonBottom}" value="Go!" type="submit"/></span>
{/if}
</div>

{/if} {* numPages > 1 *}

{/if} {* pager *}
