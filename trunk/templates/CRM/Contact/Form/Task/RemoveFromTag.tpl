{* template to remove tags from contact  *}
<div class="form-item">
<fieldset>
<legend>
{ts}Tag Contact(s) (Remove){/ts}
</legend>
<dl>
 <dt></dt>
  <dd>
   <div class="listing-box">
    {foreach from=$form.tag item="tag_val"}
      <div class="{cycle values="odd-row,even-row"}">
       {$tag_val.html}
      </div>
    {/foreach}
   </div>
    </dd>
</dl>
 
<dl>
<dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
<dt></dt><dd>{$form.buttons.html}</dd>
</dl>
</fieldset>
</div>
