<div id="help">
    {ts}Changing the parameters here globally changes the date parameters for fields in that type across CiviCRM.{/ts}
</div>

{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/PreferencesDate.tpl"}
{/if}

<div id="preferencesDate">
<p></p>
    <div class="form-item">
        {strip}
    <table cellpadding="0" cellspacing="0" border="0">
        <tr class="columnheader">
            <th >{ts}Date Class{/ts}</th>
            <th >{ts}Description{/ts}</th>
            <th >{ts}Start Offset{/ts}</th>
            <th >{ts}End Offset{/ts}</th>
            <th >{ts}Format{/ts}</th>
            <th >{ts}Minute Increment{/ts}</th>
            <th ></th>
        </tr>
        {foreach from=$rows item=row}
        <tr class="{cycle values="odd-row,even-row"} {$row.class}">
	        <td>{$row.name}</td>
            <td>{$row.description}</td>
	        <td>{$row.start}</td>	
	        <td>{$row.end}</td>	
	        <td class="nowrap">{$row.format}</td>	
	        <td>{$row.minute_increment}</td>	
	        <td><span>{$row.action|replace:'xx':$row.id}</span></td>
        </tr>
        {/foreach}
    </table>
        {/strip}
</div>
