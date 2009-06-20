{* tpl for building Individual related fields *}
<table class="form-layout-compressed">
    <tr>
        <td>
            {if $form.prefix_id}
                {$form.prefix_id.label}<br/>
                {$form.prefix_id.html}
            {/if}
        </td>    
        <td>
            {$form.first_name.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_contact' field='first_name' id=$contactId}{/if}<br />
            {$form.first_name.html}
        </td>
        <td>
            {$form.middle_name.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_contact' field='middle_name' id=$contactId}{/if}<br />
            {$form.middle_name.html}
        </td>
        <td>
            {$form.last_name.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_contact' field='last_name' id=$contactId}{/if}<br />
            {$form.last_name.html}
        </td>
        <td>
            {if $form.prefix_id}
                {$form.prefix_id.label}<br/>
                {$form.prefix_id.html}
            {/if}
        </td>
    </tr>
    
    <tr>
        <td colspan="2">
            {$form.current_employer.label}<br />
            <div align="right">
                {$form.current_employer.html}
            </div>
        </td>
                
        <td>
            {$form.job_title.label}<br />
            {$form.job_title.html}
        </td>
        <td colspan="2">
            {$form.nick_name.label}<br />
            {$form.nick_name.html|crmReplace:class:big}
        </td>
    </tr>
</table>