{if $pager and $pager->_response}
    {if $pager->_response.numPages > 1}
        <div class="crm-pager">
          <span class="crm-pager-nav">
          {$pager->_response.first}&nbsp;
          {$pager->_response.back}&nbsp;
          <strong>{$pager->_response.status}</strong>&nbsp;
          {$pager->_response.next}&nbsp;
          {$pager->_response.last}&nbsp;
          </span>
{if ! isset($noForm) || ! $noForm}
          <span class="element-right">
          {if $location eq 'top'}
            {$pager->_response.titleTop}&nbsp;<input name="{$pager->_response.buttonTop}" value="{ts}Go{/ts}" type="submit"/>
          {else}
            {$pager->_response.titleBottom}&nbsp;<input name="{$pager->_response.buttonBottom}" value="{ts}Go{/ts}" type="submit"/>
          {/if}
          </span>
{/if}
        </div>
    {/if}
    
    {* Controller for 'Rows Per Page' *}
    {if $location eq 'bottom' and $pager->_totalItems > 25}
     <div class="form-item float-right">
           <label>{ts}Rows per page:{/ts}</label> &nbsp; 
           {$pager->_response.twentyfive}&nbsp; | &nbsp;
           {$pager->_response.fifty}&nbsp; | &nbsp;
           {$pager->_response.onehundred}&nbsp; 
     </div>
     <div class="spacer"></div>
    {/if}

{/if}
