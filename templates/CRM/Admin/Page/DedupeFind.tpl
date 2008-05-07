{if $action eq 1 or $action eq 2 or $action eq 8}
  {include file="CRM/Admin/Form/DedupeFind.tpl"}
{else}
    {capture assign="rulesURL"}{crmURL p='civicrm/admin/deduperules' q="reset=1"}{/capture}
    {capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
    <div id="help">
        <p>{ts 1="http://wiki.civicrm.org/confluence//x/8zo" 2=$docURLTitle 3=$rulesURL}<strong>Find Duplicate Contacts</strong> searches through your existing contacts to identify 'suspected' duplicate records - using the <strong><a href='%3'>Duplicate Contact Rules</a></strong> which you've configured for your site (<a href='%1' target='_blank' title='%2'>read more...</a>){/ts}</p>
        <p>{ts}Click <strong>Use Rule</strong> next to the type of contact for which you want to look for duplicates.{/ts}</p>
    </div>
    {if $rows}
        <div id="browseValues">
          <div class="form-item">
            {strip}
              <table>
                <tr class="columnheader">
                  <th>{ts}Contact Type{/ts}</th>
                  <th>{ts}Level{/ts}</th>
                  <th></th>
                </tr>
                {foreach from=$rows item=row}
                  <tr class="{cycle values="odd-row,even-row"} {$row.class}">
                    <td>{$row.contact_type_display}</td>	
                    <td>{$row.level}</td>	
                    <td>{$row.action}</td>
                  </tr>
                {/foreach}
              </table>
            {/strip}
          </div>
        </div>
    {/if}
{/if}
