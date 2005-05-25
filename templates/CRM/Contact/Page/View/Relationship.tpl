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
        <div><label>Current Relationships</label></div>
        {strip}
        <table>
        <tr class="columnheader">
            <th>Relationship</th>
            <th></th>
            <th>City</th>
            <th>State/Prov</th>
            <th>Email</th>
            <th>Phone</th>
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
            <td class="nowrap"><a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=view&rtype=$rtype"}">View</a> | <a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=update&rtype=`$rel.rtype`"}">Edit</a> | <a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=disable"}" onclick = 'return confirm("Are you sure you want to disable {$rel.relation|escape:"htmlall"} relationship with {$rel.name|escape:"htmlall"}?");'> Disable</a> | <a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=delete"}" onclick = 'return confirm("Are you sure you want to delete {$rel.relation|escape:"htmlall"} relationship with {$rel.name|escape:"htmlall"}?");'> Delete</a> </td>
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
           <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
           <dd>There are no Relationships entered for this contact. You can <a href="{crmURL p='civicrm/contact/view/rel' q="action=add"}">add one</a>.</dd>
           </dl>
      </div>
  {/if}
{else}

  <div>
    {if $action NEQ 1 AND $action NEQ 2}
            <div class="action-link">
                <a href="{crmURL p='civicrm/contact/view/rel' q="cid=`$contactId`&action=add&reset=1"}">&raquo; New Relationship</a>
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
            <th>Relationship</th>
            <th></th>
            <th>City</th>
            <th>State/Prov</th>
            <th>Email</th>
            <th>End Date</th>
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
            <td>{$rel.end_date}</td>
            <td class="nowrap"><a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=view&rtype=$rtype"}">View</a> | <a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=update&rtype=$rtype"}">Edit</a> | <a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=delete"}" onclick = 'return confirm("Are you sure you want to delete {$rel.relation|escape:"htmlall"} relationship with {$rel.name|escape:"htmlall"}?");'> Delete</a> </td>
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
        <div class="label font-red">Disabled Relationships</label></div>
        <div class="description">{ts}These relationships have been marked as disabled (no longer active).{/ts}</div>
        {strip}
        <table>
        <tr class="columnheader">
            <th>Relationship</th>
            <th></th>
            <th>City</th>
            <th>State/Prov</th>
            <th>Email</th>
            <th>Phone</th>
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
            <td class="nowrap"><a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=view&rtype=$rtype"}">View</a> | <a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=update&rtype=$rtype"}">Edit</a> | <a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=enable"}" onclick = 'return confirm("Are you sure you want to enable {$rel.relation|escape:"htmlall"} relationship with {$rel.name|escape:"htmlall"}?");'> Enable</a> | <a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=delete"}" onclick = 'return confirm("Are you sure you want to delete {$rel.relation|escape:"htmlall"} relationship with {$rel.name|escape:"htmlall"}?");'> Delete</a> </td>
          </tr>
        {/foreach}
        </table>
        {/strip}
        </p>
      </div>    
{/if}
{* end of code to show disabled relationships *}
