{if $action eq 1 or $action eq 2}
  {include file="CRM/Admin/Form/DedupeRules.tpl"}
{elseif $action eq 4}
{include file="CRM/Admin/Form/DedupeFind.tpl"}
{else}
    <div id="help">
        {ts}Manage the rules used to identify potentially duplicate contact records. Scan for duplicates using a selected rule and merge duplicate contact data as needed.{/ts} {help id="id-dedupe-intro"}
    </div>
    {if $rows}
        <div id="browseValues">
          <div class="form-item">
            {strip}
              <table>
                <tr class="columnheader">
                  <th>{ts}Name{/ts}</th>
                  <th>{ts}Contact Type{/ts}</th>
                  <th>{ts}Level{/ts}</th>
                  <th>{ts}Default?{/ts}</th>
                  <th></th>
                </tr>
                {foreach from=$rows item=row}
                  <tr class="{cycle values="odd-row,even-row"}">
                    <td>{if isset($row.name)}{$row.name}{/if}</td>
                    <td>{$row.contact_type_display}</td>	
                    <td>{$row.level}</td>	
                    {if $row.is_default}
                        <td><img src="{$config->resourceBase}/i/check.gif" alt="{ts}Default{/ts}" /></td>    
                    {else}
                        <td></td>
                    {/if}
                    <td class="btn-slide" id={$row.id}>{$row.action|replace:'xx':$row.id}</td>
                  </tr>
                {/foreach}
              </table>
            {/strip}
          </div>
        </div>
    {/if}
	    <div class="action-link">
    	<a href="{crmURL q="action=add&contact_type=Individual&reset=1"}" class="button"><span>&raquo; {ts}New Dedupe Rule for Individuals{/ts}</span></a><br/><br/>
    	<a href="{crmURL q="action=add&contact_type=Household&reset=1"}" class="button"><span>&raquo; {ts}New Dedupe Rule for Households{/ts}</span></a><br/><br/>
    	<a href="{crmURL q="action=add&contact_type=Organization&reset=1"}" class="button"><span>&raquo; {ts}New Dedupe Rule for Organizations{/ts}</span></a>
        </div>

{/if}
