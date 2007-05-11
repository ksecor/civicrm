<fieldset>
  <legend>Search Crieria</legent>
<div id="searchForm">
<div class="form-item">
<table class = "form-layout">
        <tr> 
{foreach from=$customFields item=details key=customID}
{if $details.loc == 'top'}
{assign var="customField" value="custom_"|cat:$customID}
            <td align='left' class="nowrap">{$form.$customField.label}<br/>{$form.$customField.html}</td> 
{/if}
{/foreach}
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
            <td class="label">{$form.first_name.label}</td>
            <td class="nowrap">{$form.first_name.html}</td>
        </tr>
        <tr> 
            <td class="label">{$form.middle_name.label}</td>
            <td class="nowrap">{$form.middle_name.html}</td>
        </tr>
        <tr> 
            <td class="label">{$form.last_name.label}</td>
            <td class="nowrap">{$form.last_name.html}</td>
        </tr>
        <tr> 
            <td class="label">{$form.organization_name.label}</td>
            <td class="nowrap">{$form.organization_name.html}</td>
        </tr>
        <tr> 
            <td class="label">{$form.gender.label}</td>
            <td class="nowrap">{$form.gender.html}</td>
        </tr>
        <tr> 
            <td class="label">{$form.email.label}</td>
            <td class="nowrap">{$form.email.html}</td>
        </tr>
        <tr> 
            <td class="label">{$form.city.label}</td>
            <td class="nowrap">{$form.city.html}</td>
        </tr>
        <tr> 
            <td class="label">{$form.state_province.label}</td> 
            <td class="nowrap">{$form.state_province.html}</td>
        </tr>
        <tr> 
            <td class="label">{$form.postal_code.label}</td>
            <td class="nowrap">{$form.postal_code.html}</td>
        </tr>
        <tr> 
            <td class="label">{$form.country.label}</td> 
            <td>{$form.country.html}</td>
        </tr>
{foreach from=$customFields item=details key=customID}
{if $details.loc == 'bottom'}
{assign var="customField" value="custom_"|cat:$customID}
        <tr> 
            <td class="label">{$form.$customField.label}</td> 
            <td>{$form.$customField.html}</td>
        </tr>
{/if}
{/foreach}
        <tr> 
            <td colspan=2>{$form.buttons.html}</td>
        </tr>
</table>
</div>
</div>
</fieldset>

{if $rows}
Search Count: <b>{$rowCount}</b>
<table>
<tr><th>Contact ID</th><th>Sort Name</th></tr>
{foreach from=$rows key=id item=row}
<tr><td>{$row.contact_id}</td><td>{$row.sort_name}</td></tr>
{/foreach}
</table>
{/if}

