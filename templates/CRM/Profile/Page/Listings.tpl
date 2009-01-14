{* make sure there are some fields in the selector *}
{if ! empty( $columnHeaders ) || $isReset }

{if $search}
{include file="$searchTPL"}
{/if}

{* show profile listings criteria ($qill) *}
{if $rows}
    {include file="CRM/common/pager.tpl" location="top"}
    {* Search criteria are passed to tpl in the $qill array *}
    {if $qill}
     <p>
     <div id="search-status">
        {ts}Displaying contacts where:{/ts}
        {include file="CRM/common/displaySearchCriteria.tpl"}
        {if $mapURL}<a href="{$mapURL}">&raquo; {ts}Map these contacts{/ts}</a>{/if}
    </div>
    </p>
    {/if}

    {strip}
    <table>
      <tr class="columnheader">
      {foreach from=$columnHeaders item=header}
        <th scope="col">
        {if $header.sort} 
          {assign var='key' value=$header.sort} 
          {$sort->_response.$key.link} 
        {else} 
          {$header.name} 
        {/if} 
         </th>
      {/foreach}
      </tr>
    
      {counter start=0 skip=1 print=false}
      {foreach from=$rows item=row name=listings}
      <tr id="row-{$smarty.foreach.listings.iteration}" class="{cycle values="odd-row,even-row"}">
      {foreach from=$row item=value}
        <td>{$value}</td>
      {/foreach}
      </tr>
      {/foreach}
    </table>
    {/strip}
    {include file="CRM/common/pager.tpl" location="bottom"}
{elseif ! $isReset}
    {include file="CRM/Contact/Form/Search/EmptyResults.tpl" context="Profile"}
{/if}


{else}
    <div class="messages status">
      <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
        <dd>{ts}No fields in this Profile have been configured to display as columns in the listings (selector) table. Ask the site administrator to check the Profile setup.{/ts}</dd>
      </dl>
    </div>
{/if}
