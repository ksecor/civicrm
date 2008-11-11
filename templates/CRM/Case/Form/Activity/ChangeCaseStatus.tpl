{* Template for "Change Case Status" activities. *}
    <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td></tr>     
    {if $groupTree}
        <tr>
            <td colspan="2">{include file="CRM/Custom/Form/CustomData.tpl" noPostCustomButton=1}</td>
        </tr>
    {/if}
    <tr>
        <td>&nbsp;</td><td class="buttons">{$form.buttons.html}</td>
    </tr>
