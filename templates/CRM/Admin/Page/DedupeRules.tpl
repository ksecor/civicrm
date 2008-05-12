{if $action eq 1 or $action eq 2 or $action eq 8}
  {include file="CRM/Admin/Form/DedupeRules.tpl"}
{elseif $action eq 4}
{include file="CRM/Admin/Form/DedupeFind.tpl"}
{else}
    {capture assign="findURL"}{crmURL p='civicrm/admin/dedupefind' q="reset=1"}{/capture}
    {capture assign="contactMatchURL"}{crmURL p='civicrm/admin/dupematch' q="reset=1"}{/capture}
    {capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
    <div id="help">
        <p>{ts 1="http://wiki.civicrm.org/confluence//x/xis" 2=$docURLTitle}<strong>Duplicate Contact Rules</strong> are used to search through your existing contacts and identify 'suspected' duplicate contact records. Click <strong>Edit Rule</strong> to review or modify the rules for each type of contact (<a href='%1' target='_blank' title='%2'>read more...</a>).{/ts}</p>
        <p>{ts 1=$config->userFramework 2=$contactMatchURL}NOTE: These rules are NOT used for matching and synchronizing %1 users to CiviCRM contacts. This process uses <a href='%2'>Contact Matching Rules</a>.{/ts}</p>        <p>{ts}Click <strong>Use Rule</strong> next to the type of contact for which you want to look for duplicates.{/ts}</p>

    </div>
    {if $rows}
        <div id="browseValues">
          <div class="form-item">
            {strip}
              <table>
                <tr class="columnheader">
                  <th>{ts}Contact Type{/ts}</th>
                  <th>{ts}Thershold{/ts}</th>
                  <th>{ts}Level{/ts}</th>
                  <th>{ts}Active{/ts}</th>
                  <th></th>
                </tr>
                {foreach from=$rows item=row}
                  <tr class="{cycle values="odd-row,even-row"} {$row.class}">
                    <td>{$row.contact_type_display}</td>	
                    <td>{$row.threshold}</td>
                    <td>{$row.level}</td>	
                    {if $row.is_active}
                    <td>yes</td>    
                    {else}
                    <td>no</td>
                    {/if}
                    <td>{$row.action}</td>
                  </tr>
                {/foreach}
              </table>
            {/strip}
          </div>
        </div>
    {/if}
{/if}
