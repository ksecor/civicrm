{* Relationship tab within View Contact - browse, and view relationships for a contact *}

{if $action eq 1 or $action eq 2 or $action eq 4} {* add, update or view *}
    {include file="CRM/Contact/Form/Relationship.tpl"}
    <br />
{/if}

{* start of code to show current relationships *}
{if $currentRelationships}
    {* show browse table for any action *}
      <div id="current-relationships">
        <p>
        <div><label>{ts}Current Relationships{/ts}</label></div>
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Relationship{/ts}</th>
            <th></th>
            <th>{ts}City{/ts}</th>
            <th>{ts}State/Prov{/ts}</th>
            <th>{ts}Email{/ts}</th>
            <th>{ts}Phone{/ts}</th>
            <th>&nbsp;</th>
        </tr>

        {foreach from=$currentRelationships item=rel}
          {*assign var = "rtype" value = "" }
          {if $rel.contact_a eq $contactId }
            {assign var = "rtype" value = "a_b" }
          {else}
            {assign var = "rtype" value = "b_a" }
          {/if*}
          <tr class="{cycle values="odd-row,even-row"}">
            <td class="label">{$rel.relation}</td>
            <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$rel.cid`"}">{$rel.name}</a></td>
            <td>{$rel.city}</td>
            <td>{$rel.state}</td>
            <td>{$rel.email}</td>
            <td>{$rel.phone}</td>
            <td class="nowrap">{$rel.action}</td>
          </tr>
        {/foreach}
        </table>
        {/strip}
        </p>
      </div>
{/if}
{* end of code to show current relationships *}

{if NOT ($currentRelationships or $pastRelationships or $disableRelationships) }

  {if $action NEQ 1} {* show 'no relationships' message - unless already in 'add' mode. *}
       <div class="message status">
           <dl>
           <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
           {capture assign=crmURL}{crmURL p='civicrm/contact/view/rel' q="action=add"}{/capture}
           <dd>{ts 1=$crmURL}There are no Relationships entered for this contact. You can <a href="%1">add one</a>.{/ts}</dd>
           </dl>
      </div>
  {/if}
{else}

  <div>
    {if $action NEQ 1 AND $action NEQ 2}
            <div class="action-link">
                <a href="{crmURL p='civicrm/contact/view/rel' q="cid=`$contactId`&action=add&reset=1"}">&raquo; {ts}New Relationship{/ts}</a>
            </div>
        {/if}
  </div>

{/if}
<br />

{* start of code to show past relationships *}
{if $pastRelationships}
    {* show browse table for any action *}
      <div id="past-relationships">
        <p>
        <div class="label font-red">{ts}Past Relationships{/ts}</div>
        <div class="description">{ts}These relationships have a past End Date.{/ts}</div>

        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Relationship{/ts}</th>
            <th></th>
            <th>{ts}City{/ts}</th>
            <th>{ts}State/Prov{/ts}</th>
            <th>{ts}Email{/ts}</th>
            <th>{ts}End Date{/ts}</th>
            <th>&nbsp;</th>
        </tr>

        {foreach from=$pastRelationships item=rel}
          {assign var = "rtype" value = "" }
          {if $rel.contact_a > 0 }
            {assign var = "rtype" value = "b_a" }
          {else}
            {assign var = "rtype" value = "a_b" }
          {/if}
          <tr class="{cycle values="odd-row,even-row"}">
            <td class="label">{$rel.relation}</td>
            <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$rel.cid`"}">{$rel.name}</a></td>
            <td>{$rel.city}</td>
            <td>{$rel.state}</td>
            <td>{$rel.email}</td>
            <td>{$rel.end_date|crmDate}</td>
            <td class="nowrap">{$rel.action}</td>
          </tr>
        {/foreach}
        </table>
        {/strip}
        </p>
      </div>    
{/if}
{* end of code to show past relationships *}

{* start of code to show disabled relationships *}
{if $disableRelationships}
    {* show browse table for any action *}
      <div id="disabled-relationships">
        <p>
        <div class="label font-red">{ts}Disabled Relationships{/ts}</div>
        <div class="description">{ts}These relationships have been marked as disabled (no longer active).{/ts}</div>
        {strip}
        <table>
        <tr class="columnheader">
            <th>{ts}Relationship{/ts}</th>
            <th></th>
            <th>{ts}City{/ts}</th>
            <th>{ts}State/Prov{/ts}</th>
            <th>{ts}Email{/ts}</th>
            <th>{ts}Phone{/ts}</th>
            <th>&nbsp;</th>
        </tr>

        {foreach from=$disableRelationships item=rel}
          {assign var = "rtype" value = "" }
          {if $rel.contact_a > 0 }
            {assign var = "rtype" value = "b_a" }
          {else}
            {assign var = "rtype" value = "a_b" }
          {/if}
          <tr class="{cycle values="odd-row,even-row"}">
            <td class="label">{$rel.relation}</td>
            <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$rel.cid`"}">{$rel.name}</a></td>
            <td>{$rel.city}</td>
            <td>{$rel.state}</td>
            <td>{$rel.email}</td>
            <td>{$rel.phone}</td>
            <td class="nowrap">{$rel.action}</td>
          </tr>
        {/foreach}
        </table>
        {/strip}
        </p>
      </div>    
{/if}
{* end of code to show disabled relationships *}
