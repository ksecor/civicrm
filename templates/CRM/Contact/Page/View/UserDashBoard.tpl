<table class="no-border">
    <tr><td>{$displayName}</td></tr>
    <tr>
        <td>
            <fieldset><legend>{ts}Groups{/ts}</legend>
            {include file="CRM/Contact/Page/View/UserDashBoard/GroupContact.tpl"}	
            </fieldset>
        </td>
    </tr>

    {foreach from=$components key=componentName item=group}
    <tr>
        <td>
            <fieldset><legend>{$componentName}</legend>
            </fieldset>
        </td>
    </tr>
    {/foreach}
</table>