<div id="name" class="data-group form-item">
    <p>
	<label>{$displayName}</label>
    </p>
</div>

{if $op eq 'view'}
<fieldset>
<div class="form-item">
{$note.modified_date|date_format:"%B %e, %Y"}
{$note.note}
</div>
</fieldset>
{elseif $op eq 'add' or $op eq 'edit'}
<form {$form.attributes}>
<fieldset>
<div class="form-item">
{$form.note.label} {$form.note.html}
<br/>
{$form.buttons.html}
</div>
</fieldset>
</form>
{/if}

<div id="notes">
 <p>
    <div class="form-item">
   <table border=0>
   {foreach from=$notes item=note}
     <tr class="{cycle values="odd-row,even-row"}">
       <td>{$note.note|truncate:150:"...":true}</td><td width="100">{$note.modified_date|date_format:"%B %e, %Y"}</td>
       <td width="90"><a href="{$config->httpBase}contact/view/note&nid={$note.id}&op=view">View</a> | <a href="{$config->httpBase}contact/view/note&nid={$note.id}&op=edit">Edit</a></td>
     </tr>
   {/foreach}
   </table>
     <br><!--a href="#">New Note</a-->
     <input type="button" name="add_note" value="New Note" onClick="location.href='{$config->httpBase}contact/view/note&op=add';">
    </div>
 </p>
</div>
