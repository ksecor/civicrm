{* tpl for building Organization related fields *}
<table class="form-layout-compressed">
    <tr>
       <td>{$form.organization_name.label}<br/>
       {$form.organization_name.html|crmReplace:class:big}</td>

       <td>{$form.legal_name.label}<br/>
       {$form.legal_name.html|crmReplace:class:big}</td>

       <td>{$form.nick_name.label}<br/>
       {$form.nick_name.html|crmReplace:class:big}</td>

       <td>{$form.sic_code.label}<br/>
       {$form.sic_code.html|crmReplace:class:big}</td>
     </tr>
</table>
