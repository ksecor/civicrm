{if $pager and $pager->_response}

{if $pager->_response.numPages >= 1}

<div id="crm-pager">
  <span class="crm-pager-nav">
  {$pager->_response.low}&nbsp;
  {$pager->_response.medium}&nbsp;
  {$pager->_response.high}&nbsp;
  {$pager->_response.first}&nbsp;
  {$pager->_response.back}&nbsp;
  {$pager->_response.status}&nbsp;
  {$pager->_response.next}&nbsp;
  {$pager->_response.last}&nbsp;</span>
  {if $location eq 'top'}
    <span class="element-right">{$pager->_response.titleTop}&nbsp;<input name="{$pager->_response.buttonTop}" value="Go!" type="submit" class="two"/></span>
  {else}
    <span class="element-right">{$pager->_response.titleBottom}&nbsp;<input name="{$pager->_response.buttonBottom}" value="Go!" type="submit" class="two"/></span>
  {/if}
</div>

{/if} {* numPages > 1 *}

{/if} {* pager *}
