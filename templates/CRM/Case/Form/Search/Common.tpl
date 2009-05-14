{if $notConfigured} {* Case types not present. Component is not configured for use. *}
    {include file="CRM/Case/Page/ConfigureError.tpl"}
{else}
<tr>
  <td width="25%"><label>{ts}Case Type{/ts}</label>
    <br />
      <div class="listing-box" style="width: auto; height: 120px">
       {foreach from=$form.case_type_id item="case_type_id_val"}
        <div class="{cycle values="odd-row,even-row"}">
                {$case_type_id_val.html}
        </div>
      {/foreach}
      </div><br />
  </td>
  
  <td>
    {$form.case_status_id.label}<br /> 
    {$form.case_status_id.html}<br /><br />	
    {$form.case_owner.html} &nbsp;<a href="javascript:unselectRadio('case_owner', '{$form.formName}')">unselect</a><br />
    {if $form.case_deleted}	
        {$form.case_deleted.html}	
        {$form.case_deleted.label}	
    {/if}
  </td>

</tr>     
{/if}