<tr> 
    <td><label>{ts}Membership Type(s){/ts}</label><br />
                   <div class="listing-box">
                    {foreach from=$form.member_membership_type_id item="membership_type_val"} 
                    <div class="{cycle values="odd-row,even-row"}">
                    {$membership_type_val.html}
                    </div>
                    {/foreach}
                </div>
    </td>
    <td><label>{ts}Membership Status{/ts}</label><br />
                <div class="listing-box">
                    {foreach from=$form.member_status_id item="membership_status_val"} 
                    <div class="{cycle values="odd-row,even-row"}">
                    {$membership_status_val.html}
                    </div>
                    {/foreach}
                </div>
    </td>
</tr>

<tr>
    <td>
     {$form.member_source.label}
     <br />{$form.member_source.html}
    </td>
    <td>
     {$form.member_is_primary.html} {help id="id-member_is_primary" file="CRM/Member/Form/Search.hlp"}<br />
     {$form.member_pay_later.html}&nbsp;{$form.member_pay_later.label}<br />
     {$form.member_test.html}&nbsp;{$form.member_test.label}
    </td> 
</tr>
<tr> 
    <td> 
     {$form.member_join_date_low.label} 
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=member_join_date_low}
    </td>
    <td> 
     {$form.member_join_date_high.label} <br />
     {include file="CRM/common/jcalendar.tpl" elementName=member_join_date_high}
    </td> 
</tr> 
<tr> 
    <td> 
     {$form.member_start_date_low.label} 
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=member_start_date_low}
    </td>
    <td>
     {$form.member_start_date_high.label}
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=member_start_date_high}
    </td> 
</tr> 
<tr> 
    <td>  
     {$form.member_end_date_low.label} 
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=member_end_date_low}
    </td>
    <td> 
     {$form.member_end_date_high.label}
     <br />
     {include file="CRM/common/jcalendar.tpl" elementName=member_end_date_high}
    </td> 
</tr> 
{if $membershipGroupTree}
<tr>
    <td colspan="4">
    {include file="CRM/Custom/Form/Search.tpl" groupTree=$membershipGroupTree showHideLinks=false}
    </td>
</tr>
{/if}
