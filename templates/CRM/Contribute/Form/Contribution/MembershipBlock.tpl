{if $membershipBlock}
<div id="membership">
 {if $context EQ "makeContribution"}
  <fieldset>    
   <div class="form-item">
        {if $membershipBlock.new_title}
            <legend>{$membershipBlock.new_title}</legend>
        {/if}
        {if $membershipBlock.new_text}
            <div id=membership-intro>
                <p>{$membershipBlock.new_text}</p>
            </div> 
        {/if}
  {/if}
    {if $context EQ "confirmContribution" OR $context EQ "thankContribution"}
        <div class="header-dark">
            {if $membershipBlock.new_title}
                {$membershipBlock.new_title}
            {else}
                {ts}Your Membership Selection{/ts}
            {/if}
        </div>
    {/if}
    {if $preview}
        {assign var="showSelectOptions" value="1"}
    {/if}
    {strip}
        <table id="membership-listings" class="no-border">
        {foreach from=$membershipTypes item=row}
        <tr {if $context EQ "makeContribution"}class="odd-row" {/if}valign="top">
            {if $showRadio }
                {assign var="pid" value=$row.id}
                <td>{$form.selectMembership.$pid.html}</td>
            {/if}
          <td>
                <strong>{$row.name}</strong><br />
                {$row.description} &nbsp;
                {if ($membershipBlock.display_min_fee AND $context EQ "makeContribution") AND $row.minimum_fee GT 0 }
                    {ts 1=$row.minimum_fee|crmMoney}(Contribute at least %1 to be eligible for this membership .){/ts}
                {/if}
            
            {if $context EQ "thankContribution"}
                
            {/if}
            </td>
            
            <td>
              {if $row.current_membership }   
               {ts 1=$row.current_membership|crmDate}You Are Current Member of this<strong> Membership</strong> (Membership Will Expire on %1){/ts}
              {/if}
           </td> 
        </tr>
        
        {/foreach}
        {if $showRadio AND !$preview }
            <tr class="odd-row"><td colspan="4">{$form.selectMembership.no_thanks.html}</td></tr> 
        {/if}          
        </table>
    {/strip}
    {if $context EQ "makeContribution"}
        </fieldset>
    {/if}
</div>
{/if}

