{* Admin page for Duplicate Matching configuration *}
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/DupeMatch.tpl"}
{else}
    <div id="help">
    {capture assign=crmURL}{crmURL q="action=update&reset=1&advance=0"}{/capture}
        <p>{ts 1=$crmURL}CiviCRM uses a configurable Duplicate Matching Rule to determine when a new Individual contact should be flagged as a potential duplicate of an existing record. The default configuration compares email address AND first name AND last name. This rule is used when entering a new individual contact, updating an existing contact, and importing contacts with the Import Wizard. Click <a href="%1">Edit Rule</a> to modify the set of contact fields used for identifying duplicate contacts.{/ts}</p>
    </div>
{/if}

{if $rows}
<div id="browseValues">
    <div class="form-item">
    {strip}
    <table>
        
	<tr class="columnheader">
        <th>{ts}Duplicate Matching Rule{/ts}</th>
        <th></th>
    </tr>
    {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}">
	        <td>{$row.rule}</td>	
	        
	        <td>{$row.action}</td>
        </tr>
    {/foreach}
	
    </table>
    {/strip}
    </div>
</div>
    
{/if}
