{if $membershipBlock}
{if $singleMembership && $context EQ "makeContribution"}
     {$singleMembership.html}                          
{else}
<div id="membership">
 {if $context EQ "makeContribution"}
  <fieldset>    
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
                        {ts}Select a Membership Renewal Level{/ts}
                    {/if}

            {else}
                    {if $membershipBlock.new_title}
                        {$membershipBlock.new_title}
                    {else}
                        {ts}Select a Membership Level{/ts}
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
                    {if $is_separate_payment OR ! $form.amount.label}
                        {ts 1=$row.minimum_fee|crmMoney}(membership fee - %1){/ts}
                    {else}
                        {ts 1=$row.minimum_fee|crmMoney}(contribute at least %1 to be eligible for this membership){/ts}
                    {/if}
                {/if}
                        
           </td>
            
            <td>
              {if $row.current_membership AND $context EQ "makeContribution" }
               <br /><em>{ts 1=$row.current_membership|crmDate 2=$row.name}Your current <strong>%2</strong> membership expires on %1.{/ts}</em>
              {/if}
           </td> 
        </tr>
        
        {/foreach}
        {if $showRadio}
            {if $showRadioNoThanks }
            <tr class="odd-row">
              <td>{$form.selectMembership.no_thanks.html}</td>
              <td colspan="2"><strong>No thank you</strong></td>      
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
