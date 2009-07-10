{* tpl for building Household related fields *}
<table class="form-layout-compressed">
    <tr>
       <td>{$form.household_name.label}<br/>
       {$form.household_name.html|crmReplace:class:big}</td>

       <td>{$form.nick_name.label}<br/>
       {$form.nick_name.html|crmReplace:class:big}</td>
     </tr>
</table>