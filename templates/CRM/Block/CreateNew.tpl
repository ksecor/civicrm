<div class="menu">
<select class="form-select" id="civicrm-shortcuts" name="civicrm-shortcuts" onChange="if (this.value) location.href=this.value;">
	<option value="">{ts}- create new -{/ts}</option>
	{foreach from=$shortCuts item=short}
	    <option value="{$short.url}" class="{$short.ref}">{$short.title}</option>
    {/foreach}
</select>
</div>
