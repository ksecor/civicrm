{* Relationship tab within View Contact - browse, and view relationships for a contact *}

{if $action eq 1 or $action eq 2 or $action eq 4} {* add, update or view *}
    {include file="CRM/Contact/Form/Relationship.tpl"}
        
{/if}

{if $relationship}
    {* show browse table for any action *}
      <div id="relationships">
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

        {foreach from=$relationship item=rel}
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
            <td class="nowrap"><a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=view&rtype=$rtype"}">View</a> | <a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=update&rtype=$rtype"}">Edit</a> |<a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=delete"}" onclick = 'return confirm("Are you sure you want to delete {$rel.relation} relationship with {$rel.name}?");'> Delete</a> </td>
          </tr>
        {/foreach}
        </table>
        {/strip}
        </p>
        {if $action NEQ 1 AND $action NEQ 2}
            <div class="action-link">
                <a href="{crmURL p='civicrm/contact/view/rel' q="cid=`$contactId`&action=add"}">&raquo; New Relationship</a>
            </div>
        {/if}
      </div>

{elseif $action NEQ 1} {* show 'no relationships' message - unless already in 'add' mode. *}
       <div class="message status">
           <dl>
           <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
           <dd>There are no Relationships entered for this contact. You can <a href="{crmURL p='civicrm/contact/view/rel' q="action=add"}">add one</a>.</dd>
           </dl>
      </div>
{/if}

