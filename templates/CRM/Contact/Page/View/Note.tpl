<div id="name" class="data-group form-item">
    <p>
	<label>{$displayName}</label>
    </p>
</div>

{if $op eq 'view'}
    <p>
    <fieldset><legend>View Note</legend>
    <div class="form-item">
        <label>Date:</label> {$note.modified_date|date_format:"%B %e, %Y"}
        <p>{$note.note}</p>
    </div>
    </fieldset>
    </p>
{elseif $op eq 'add' or $op eq 'edit'}
    <form {$form.attributes}>
    <p>
    <fieldset><legend>{if $op eq 'add'}New{else}Edit{/if} Note</legend>
    <div class="form-item">
        {$form.note.html}
        <br/>
        {$form.buttons.html}
    </div>
    </fieldset>
    </p>
    </form>
{/if}

<div id="notes">
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
	<th>Note Listings</th>
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
                    <a href="{$config->httpBase}contact/view/note&cid={$contactId}&nid={$note.id}&op=view">(more)</a>
                {/if}
            </td>
            <td>{$note.modified_date|date_format:"%B %e, %Y"}</td>
            <td><a href="{$config->httpBase}contact/view/note&cid={$contactId}&nid={$note.id}&op=view">View</a> | <a href="{$config->httpBase}contact/view/note&cid={$contactId}&nid={$note.id}&op=edit">Edit</a></td>
         </tr>
       {/foreach}
       </table>
       {/strip}
       <br />
       <div class="action-link">
         <a href="{$config->httpBase}contact/view/note&cid={$contactId}&op=add">New Note</a>
       </div>
    </div>
 </p>
</div>
