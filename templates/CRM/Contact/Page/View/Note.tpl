{if $action eq 4}{* when action is view  *}
    {if $notes}
        <p>
        <fieldset>
          <legend>{ts}View Note{/ts}</legend>
          <div class="form-item">
            <label>{ts}Date:{/ts}</label> {$note.modified_date|crmDate}
            <p>{$note.note}</p>
            <input type="button" name='cancel' value="{ts}Done{/ts}" onClick="location.href='{crmURL p='civicrm/contact/view/note' q='action=browse'}';">        
          </div>
        </fieldset>
        </p>
    {/if}
{elseif $action eq 1 or $action eq 2} {* action is add or update *}
    <p>
    <fieldset><legend>{if $action eq 1}{ts}New Note{/ts}{else}{ts}Edit Note{/ts}{/if}</legend>
    <div class="form-item">
        {$form.note.html}
        <br/>
        {$form.buttons.html}
    </div>
    </fieldset>
    </p>
{/if}
{if ($action eq 8)}
<fieldset><legend>Delete Note</legend>
<div class=status> {ts}Are you sure you want to delete the Note " {$notes.$id.note} " ?{/ts}</div>
<dl><dt></dt><dd>{$form.buttons.html}</dd></dl>
</fieldset>

{/if}


{if $notes}
    {* show browse table for any action *}
<div id="notes">
    <div class="form-item">
    <p>
    {strip}
       <table>
       <tr class="columnheader">
	<th>{ts}Note{/ts}</th>
	<th>{ts}Date{/ts}</th>
	<th></th>
       </tr>
       {foreach from=$notes item=note}
         <tr class="{cycle values="odd-row,even-row"}">
            <td>
                {$note.note|mb_truncate:80:"...":true}
                {* Include '(more)' link to view entire note if it has been truncated *}
                {assign var="noteSize" value=$note.note|count_characters:true}
                {if $noteSize GT 80}
		  <a href="{crmURL p='civicrm/contact/view/note' q="nid=`$note.id`&action=view"}">{ts}(more){/ts}</a>
                {/if}
            </td>
            <td>{$note.modified_date|crmDate}</td>
            <td class="nowrap">{$note.action}</td>
            </td>
         </tr>
       {/foreach}
       </table>
       {/strip}

       {if $action eq 16 or $action eq 4 or $action eq 8}
       <div class="action-link">
    	 <a href="{crmURL p='civicrm/contact/view/note' q="cid=`$contactId`&action=add"}">&raquo; {ts}New Note{/ts}</a>
       </div>
       {/if}
    </div>
 </p>
</div>

{else}
   <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"></dt>
        {capture assign=crmURL}{crmURL p='civicrm/contact/view/note' q='action=add'}{/capture}
        <dd>{ts 1=$crmURL}There are no Notes for this contact. You can <a href="%1">add one</a>.{/ts}</dd>
    </dl>
   </div>
{/if}
