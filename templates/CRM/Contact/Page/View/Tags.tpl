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
<table border="1">
<tr>
<td><input type="checkbox" name="category" value="category0" />{$0.name}</td>
</tr>
<tr>
<td><input type="checkbox" name="category" value="category1" />{$1.name}</td>
</tr>
<tr>
<td><input type="checkbox" name="category" value="category2" />{$2.name}</td>
</tr>
<tr>
<td><input type="checkbox" name="category" value="category3" />{$3.name}</td>
</tr>
<tr>
<td><input type="checkbox" name="category" value="category4" />{$4.name}</td>
</tr>
</table>
</div>

<div>
<span>
        <input type="button" name="update_tags" value="Update Tags">
        <input type="button" name="cancel" value="Cancel">
</span>
</div>

</div>
