<div class="view-content">
{if $action eq 4}{* when action is view  *}
    {if $notes}
        <p></p>
        <fieldset>
          <legend>{ts}View Note{/ts}</legend>
          <div class="form-item">
            <label>{ts}Subject:{/ts}</label> {$note.subject} <br />
            <label>{ts}Date:{/ts}</label> {$note.modified_date|crmDate}
            <p>{$note.note}</p>
            <input type="button" name='cancel' value="{ts}Done{/ts}" onclick="location.href='{crmURL p='civicrm/contact/view' q='action=browse&selectedChild=note'}';"/>        
          </div>
        </fieldset>
        {/if}
{elseif $action eq 1 or $action eq 2} {* action is add or update *}
    <p></p>
    <fieldset><legend>{if $action eq 1}{ts}New Note{/ts}{else}{ts}Edit Note{/ts}{/if}</legend>
    <div class="form-item">
        {$form.subject.label} {$form.subject.html} 
        <br/><br/>
        <label for="note">{$form.note.html}</label>
        <br/>
        {$form.buttons.html}
    </div>
    </fieldset>
{/if}
{if ($action eq 8)}
<fieldset><legend>{ts}Delete Note{/ts}</legend>
<div class=status>{ts 1=$notes.$id.note}Are you sure you want to delete the note '%1'?{/ts}</div>
<dl><dt></dt><dd>{$form.buttons.html}</dd></dl>
</fieldset>

{/if}


{if $notes}
    {* show browse table for any action *}
<div id="notes">
    {strip}
        <table class="selector">
        <tr class="columnheader">
	        <th scope="col">{ts}Note{/ts}</th>
	        <th scope="col">{ts}Subject{/ts}</th>
	        <th scope="col">{ts}Date{/ts}</th>
	        <th scope="col">{ts}Created By{/ts}</th>
	        <th scope="col" title="Action Links"></th>
        </tr>
        {foreach from=$notes item=note}
        <tr class="{cycle values="odd-row,even-row"}">
            <td>
                {$note.note|mb_truncate:80:"...":true}
                {* Include '(more)' link to view entire note if it has been truncated *}
                {assign var="noteSize" value=$note.note|count_characters:true}
                {if $noteSize GT 80}
		        <a href="{crmURL p='civicrm/contact/view/note' q="action=view&selectedChild=note&reset=1&cid=`$contactId`&id=`$note.id`"}">{ts}(more){/ts}</a>
                {/if}
            </td>
            <td>{$note.subject}</td>
            <td>{$note.modified_date|crmDate}</td>
            <td>
                <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$note.contact_id`"}">{$note.createdBy}</a>
            </td>
            <td class="nowrap">{$note.action}</td>
        </tr>
        {/foreach}
        </table>
    {/strip}

    {if $permission EQ 'edit' AND ($action eq 16 or $action eq 4 or $action eq 8)}
       <div class="action-link">
    	 <a accesskey="N" href="{crmURL p='civicrm/contact/view/note' q="cid=`$contactId`&action=add"}" class="button"><span>&raquo; {ts}New Note{/ts}</span></a>
       </div>
    {/if}
 </div>

{elseif ! ($action eq 1)}
   <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
        {capture assign=crmURL}{crmURL p='civicrm/contact/view/note' q="cid=`$contactId`&action=add"}{/capture}
        <dd>{ts 1=$crmURL}There are no Notes for this contact. You can <a accesskey="N" href='%1'>add one</a>.{/ts}</dd>
    </dl>
   </div>
{/if}
</div>
