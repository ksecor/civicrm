{if $action eq 4}
{if $notes}
    <p>
    <fieldset>
      <legend>View Note</legend>
      <div class="form-item">
        <label>Date:</label> {$note.modified_date|date_format:"%B %e, %Y"}
        <p>{$note.note}</p>
      </div>
    </fieldset>
    </p>
{/if}
{elseif $action eq 1 or $action eq 2}
    <form {$form.attributes}>
    <p>
    <fieldset><legend>{if $action eq 1}New{else}Edit{/if} Note</legend>
    <div class="form-item">
        {$form.note.html}
        <br/>
        {$form.buttons.html}
    </div>
    </fieldset>
    </p>
    </form>
{/if}

{if $notes}
<div id="notes">
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
	<th>Note</th>
	<th>Date</th>
	<th></th>
       </tr>
       {foreach from=$notes item=note}
         <tr class="{cycle values="odd-row,even-row"}">
            <td>
                {$note.note|truncate:80:"...":true}
                {* Include '(more)' link to view entire note if it has been truncated *}
                {assign var="noteSize" value=$note.note|count_characters:true}
                {if $noteSize GT 80}
		  <a href="{crmURL p='civicrm/contact/view/note' q="nid=`$note.id`&action=view"}">(more)</a>
                {/if}
            </td>
            <td>{$note.modified_date|date_format:"%B %e, %Y"}</td>
            <td><a href="{crmURL p='civicrm/contact/view/note' q="nid=`$note.id`&action=view"}">View</a> | <a href="{crmURL p='civicrm/contact/view/note' q="nid=`$note.id`&action=update"}">Edit</a> | <a href="{crmURL p='civicrm/contact/view/note' q="nid=`$note.id`&action=delete"}" onclick = 'return confirm("Are you sure you want to delete  {$note.note|truncate:15:"...":true} ?");'> Delete</a>
            </td>	
         </tr>
       {/foreach}
       </table>
       {/strip}

       {if $action eq 16 or $action eq 4 or $action eq 8}
       <div class="action-link">
    	 <a href="{crmURL p='civicrm/contact/view/note' q="cid=`$contactId`&action=add"}">&raquo; New Note</a>
       </div>
       {/if}
    </div>
 </p>
</div>

{else}
   <div class="message status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="status"></dt>
        <dd>There are no Notes for this contact. You can <a href="{crmURL p='civicrm/contact/view/note' q='action=add'}">add one</a>.</dd>
    </dl>
   </div>
{/if}
