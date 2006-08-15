{if $membershipBlock}
{if $singleMembership && $context EQ "makeContribution"}
     {$singleMembership.html}                          
{else}
<div id="membership">
 {if $context EQ "makeContribution"}
  <fieldset>    
   <div class="form-item">
      {if $renewal_mode }
        {if $membershipBlock.renewal_title}
            <legend>{$membershipBlock.renewal_title}</legend>
        {/if}
        {if $membershipBlock.renewal_text}
            <div id=membership-intro>
                <p>{$membershipBlock.renewal_text}</p>
            </div> 
        {/if}

      {else}        
        {if $membershipBlock.new_title}
            <legend>{$membershipBlock.new_title}</legend>
        {/if}
        {if $membershipBlock.new_text}
            <div id=membership-intro>
                <p>{$membershipBlock.new_text}</p>
            </div> 
        {/if}
      {/if}
  {/if}
    {if  $context neq "makeContribution" }
        <div class="header-dark">
            {if $renewal_mode }
                    {if $membershipBlock.renewal_title}
                        {$membershipBlock.renewal_title}
                    {else}
                     {ts}Your Membership Selection{/ts}
                    {/if}

            {else}
                        {if $membershipBlock.new_title}
                        {$membershipBlock.new_title}
                    {else}
                     {ts}Your Membership Selection{/ts}
                    {/if}
            {/if}
        </div>
    {/if}
   
    {strip}
        <table id="membership-listings" class="no-border">
        {foreach from=$membershipTypes item=row }
        <tr {if $context EQ "makeContribution" OR $context EQ "thankContribution" }class="odd-row" {/if}valign="top">
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
                        
            </td>
            
            <td>
              {if $row.current_membership AND $context EQ "makeContribution" }   
               {ts 1=$row.current_membership|crmDate}You are current member of this<strong> Membership</strong> (Membership Will Expire on %1){/ts}
              {/if}
           </td> 
        </tr>
        
        {/foreach}
        {if $showRadio}
            {if $showRadioNoThanks }
            <tr class="odd-row">
              <td>{$form.selectMembership.no_thanks.html}</td>
              <td><strong>No thank you</strong></td>      
            </tr> 
            {/if}
        {/if}          
        </table>
    {/strip}
    {if $context EQ "makeContribution"}
        </fieldset>
    {/if}
</div>
{/if}
{/if}
