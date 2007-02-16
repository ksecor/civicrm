<table class="no-border">
    <tr><td>{$DisplayName}</td></tr>
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
            {if $componentName eq CiviContribute}
                 {*include file="CRM/Contact/Page/View/UserDashBoard/Contribution.tpl"*}	    
            {elseif $componentName eq CiviMember}
                 {include file="CRM/Contact/Page/View/UserDashBoard/Membership.tpl"}	    
            {elseif $componentName eq CiviEvent}
                {include file="CRM/Contact/Page/View/UserDashBoard/Participant.tpl"}	
            {/if}
            </fieldset>
        </td>
    </tr>
    {/foreach}
</table>