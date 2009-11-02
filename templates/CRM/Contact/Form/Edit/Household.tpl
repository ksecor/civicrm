{* tpl for building Household related fields *}
<table class="form-layout-compressed">
    <tr>
       <td>{$form.household_name.label}<br/>
        {if $action == 2}
            {include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_contact' field='household_name' id=$entityID}
        {/if}
       {$form.household_name.html|crmReplace:class:big}</td>

       <td>{$form.nick_name.label}<br/>
       {$form.nick_name.html|crmReplace:class:big}</td>

       <td>{if ($action == 1 and $contactSubType) or !$allowEditSubType}&nbsp;{else}
              {$form.contact_sub_type.label}<br />
              {$form.contact_sub_type.html}
           {/if}
       </td>
     </tr>
</table>
