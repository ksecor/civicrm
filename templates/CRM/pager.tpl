{if $pager and $pager->_response}

{if $pager->_response.numPages >= 1}

<div id="crm-pager">
  <span class="crm-pager-nav">
  {*{$pager->_response.low}&nbsp;
  {$pager->_response.medium}&nbsp;
  {$pager->_response.high}&nbsp;*}
  {$pager->_response.first}&nbsp;
  {$pager->_response.back}&nbsp;
  {$pager->_response.status}&nbsp;
  {$pager->_response.next}&nbsp;
  {$pager->_response.last}&nbsp;
  </span>
  <span class="element-right">
  {if $location eq 'top'}
    {$pager->_response.titleTop}&nbsp;<input name="{$pager->_response.buttonTop}" value="Go" type="submit"/>
  {else}
    {$pager->_response.titleBottom}&nbsp;<input name="{$pager->_response.buttonBottom}" value="Go" type="submit"/>
  {/if}
  </span>
</div>

{/if} {* numPages > 1 *}

{/if} {* pager *}
