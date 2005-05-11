{* Relationship tab within View Contact - browse, add, edit relationships for a contact *}
{if $relationship}
    {if $action eq 4} {* action = view *}
        <div class="form-item">
        <fieldset><legend>View Relationship</legend>

        <div>
        <span class="horizontal-position">
        <span class="labels"><label>Relationship :</label></span> 
        <span class="fields">{$relationship_name}</span>
        </span>
        </div>
        <div>
            <span class="horizontal-position">
        <span class="labels"><label>Contact :</label></span> 
        <span class="fields">{$relationship_contact_name}</span>
        </span>
        </div>
        <div class="spacer"></div>
        </fieldset>
        </div>    
        
    {elseif $action eq 1 or $action eq 2} {* add or update *}
        {include file="CRM/Contact/Form/Relationship.tpl"}
        	
    {/if}

    {* show browse table for any action *}
      <div id="relationships">
        <p>
        {strip}
        <table>
        <tr class="columnheader">
            <th>Relationship</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Phone</th>
            <th>City</th>
            <th>State/Prov</th>
            <th>&nbsp;</th>
        </tr>

        {foreach from=$relationship item=rel}
          {assign var = "rtype" value = "" }
          {if $rel.contact_b > 0 }
            {assign var = "rtype" value = "b_a" }
          {else}
            {assign var = "rtype" value = "a_b" }
          {/if}
            <tr class="{cycle values="odd-row,even-row"}">
                <td> {$rel.relation}</td>
                <td> <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$rel.cid`"}">{$rel.name}</a></td>
            <td>{$rel.email}</td>
            <td>{$rel.phone}</td>
            <td>{$rel.city}</td>
            <td>{$rel.state}</td>
            <td><a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=update&rtype=$rtype"}">Edit</a> | Delete</td>
            </tr>
        {/foreach}
        </table>
        {/strip}
        </p>
        <div class="action-link">
             <a href="{crmURL p='civicrm/contact/view/rel' q="cid=`$contactId`&action=add"}">New Relationship</a>
        </div>
      </div>

{else} {* no relationships to browse *}
       <div class="message status">
           <dl>
           <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
           <dd>There are no Relationships entered for this contact. You can <a href="{crmURL p='civicrm/contact/view/rel' q='action=add'}">add one</a>.</dd>
           </dl>
      </div>
{/if}

