<div class="data-group form-item">
<div>
    {if $contact_type eq 'Individual'}
        <label>{$prefix}. &nbsp;&nbsp;{$display_name}&nbsp;&nbsp; {$suffix}. </label>
    {elseif $contact_type eq 'Organization'}
        <label>{$sort_name}</label>
    {elseif $contact_type eq 'Household'}
        <label>{$sort_name}</label>
    {/if}
</div>

<div>
<h3>Tags(categories) for this contact:</h3>
{strip}
<table border="1">
<tr>
	<td>
	{if $categoryId.1 eq 1}
	<input type="checkbox" name="category" value="category1" checked="checked" />
	{else}
	<input type="checkbox" name="category" value="category1" />
	{/if}
	{$0.name}
	</td>
</tr>
<tr>
	<td>
	{if $categoryId.2 eq 2}
	<input type="checkbox" name="category" value="category2" checked="checked" />
	{else}
	<input type="checkbox" name="category" value="category2" />
	{/if}
	{$1.name}
	</td>
</tr>
<tr>
	<td>
	{if $categoryId.3 eq 3}
	<input type="checkbox" name="category" value="category3" checked="checked" />
	{else}
	<input type="checkbox" name="category" value="category3" />
	{/if}
	{$2.name}
	</td>
</tr>
<tr>
	<td>
	{if $categoryId.4 eq 4}	
	<input type="checkbox" name="category" value="category4" checked="checked" />
	{else}
	<input type="checkbox" name="category" value="category4" />
	{/if}
	{$3.name}
	</td>
</tr>
<tr>
	<td>
	{if $categoryId.5 eq 5}
	<input type="checkbox" name="category" value="category5" checked="checked" />
	{else}
	<input type="checkbox" name="category" value="category5" />
	{/if}
	{$4.name}
	</td>
</tr>
</table>
{/strip}
</div>

<div>
<span>
        <input type="button" name="update_tags" value="Update Tags">
        <input type="button" name="cancel" value="Cancel">
</span>
</div>

</div>
