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

    {foreach from=$dashboardElements item=element}
    <tr>
        <td>
            <div class="header-dark">{$element.sectionTitle}</div>	        
            {include file=$element.templatePath}
        </td>
    </tr>
    {/foreach}
</table>
