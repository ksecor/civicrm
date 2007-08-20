{* Admin page for Duplicate Matching configuration *}
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/DupeMatch.tpl"}
{else}
    <div id="help">
    {capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}
    {capture assign=crmURL}{crmURL q="action=update&reset=1&advance=0"}{/capture}
    {capture assign="rulesURL"}{crmURL p='civicrm/admin/deduperules' q="reset=1"}{/capture}
        <p>{ts 1=$crmURL 2=$config->userFramework 3="http://wiki.civicrm.org/confluence//x/9Cc" 4=$docURLTitle}The <strong>Contact Matching Rule</strong> is used to determine when a new %2 User record should be matched and linked to an existing CiviCRM contact record. It is also used to alert you of a matching Individual contact record when you create a new Individual. Finally, contact <strong>Import</strong> uses this rule to determine whether a row in your import file is a duplicate of an existing record. The default rule compares email address AND first name AND last name. Click <a href='%1'>Edit Rule</a> to modify the set of contact fields used for matching (<a href='%3' target='_blank' title='%4'>read more...</a>).{/ts}</p>
        <p>{ts 1=$rulesURL}NOTE: These rules are NOT used to <strong>Find Duplicate Contacts</strong>. That feature searches your existing contact records, and identifies 'suspected' duplicates. It uses <a href='%1'>Duplicate Contact Rules</a>.{/ts}</p>
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
