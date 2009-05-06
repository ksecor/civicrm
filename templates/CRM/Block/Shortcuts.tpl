<div class="menu">
<select class="form-select" id="civicrm-shortcuts" name="civicrm-shortcuts" onChange="if (this.value) location.href=this.value;">
	<option value="">{ts}- select -{/ts}</option>
	{foreach from=$shortCuts item=short}
	    <option value="{$short.url}">{$short.title}</option>
    {/foreach}
</select>
</div>
