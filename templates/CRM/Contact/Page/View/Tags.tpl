{debug}
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
{foreach from=$category key=category_id item=category_array}

{assign var="variable1" value=$entityCategory}

{php}
echo "category_id = $category_id<br/>";
print_r($variable1);
$checked = in_array($entityCategory, $category_id) ? "checked" : "";
{/php}
<tr>
	<td>
	<input type="checkbox" name="category" value="{$category_id}" "{$category_array.checked}" /> &nbsp; {$category_array.name} cat_id = {$category_id}
	</td>
</tr>
{/foreach}

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
