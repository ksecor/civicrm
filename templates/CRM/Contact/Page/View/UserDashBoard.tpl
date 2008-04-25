<table class="no-border">
{if $showGroup}
    <tr>
        <td>
          <div class="header-dark">
          {ts}Your Group(s){/ts}  
          </div>	  
          {include file="CRM/Contact/Page/View/UserDashBoard/GroupContact.tpl"}	
            
        </td>
    </tr>
{/if}

    {foreach from=$components key=componentName item=group}
    <tr>
        <td>
      
            {if $componentName eq CiviContribute}
                 <div class="header-dark">
                  {ts}Your Contribution(s){/ts}  
                  </div>	        
                  {include file="CRM/Contact/Page/View/UserDashBoard/Contribution.tpl"}
            {elseif $componentName eq CiviMember}
                 <div class="header-dark">
                  {ts}Your Membership(s){/ts}  
                  </div>	      
                 {include file="CRM/Contact/Page/View/UserDashBoard/Membership.tpl"}	    
            {elseif $componentName eq CiviEvent}
                 <div class="header-dark">
                  {ts}Your Event(s){/ts}  
                  </div>	      
                  {include file="CRM/Contact/Page/View/UserDashBoard/Participant.tpl"}
        	{elseif $componentName eq 'My Contacts / Organizations'}
                 <div class="header-dark">
                  {ts}Your Contacts / Organizations(s){/ts}  
                  </div>	     
                  {include file="CRM/Contact/Page/View/Relationship.tpl"}
            {/if}
    
      
        </td>
    </tr>
    {/foreach}
</table>
