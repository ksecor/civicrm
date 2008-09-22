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
                </div><br />
    </td>
</tr>

<tr>
    <td>
     {$form.member_source.label}
    <br />{$form.member_source.html}
    </td>
    <td>
     {$form.member_test.html}&nbsp;{$form.member_test.label}<br/>
     {$form.member_pay_later.html}&nbsp;{$form.member_pay_later.label}</td> 
</tr>
<tr> 
    <td> 
     {$form.member_join_date_low.label} 
    <br />
     {$form.member_join_date_low.html} &nbsp;
     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_5}
     {include file="CRM/common/calendar/body.tpl" dateVar=member_join_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_5}
    </td>
    <td> 
     {$form.member_join_date_high.label} <br />
     {$form.member_join_date_high.html}&nbsp;
     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_6}
     {include file="CRM/common/calendar/body.tpl" dateVar=member_join_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_6}
    </td> 
</tr> 
<tr> 
    <td> 
     {$form.member_start_date_low.label} 
    <br />
     {$form.member_start_date_low.html} &nbsp;
     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_1}
     {include file="CRM/common/calendar/body.tpl" dateVar=member_start_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_1}
    </td>
    <td>
     {$form.member_start_date_high.label}
    <br />
     {$form.member_start_date_high.html}&nbsp;
     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_2}
     {include file="CRM/common/calendar/body.tpl" dateVar=member_start_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_2}
    </td> 
</tr> 
<tr> 
    <td>  
     {$form.member_end_date_low.label} 
    <br />
     {$form.member_end_date_low.html} &nbsp;
     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_3}
     {include file="CRM/common/calendar/body.tpl" dateVar=member_end_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_3}
    </td>
    <td> 
     {$form.member_end_date_high.label}
    <br />
     {$form.member_end_date_high.html} &nbsp;
     {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_4}
     {include file="CRM/common/calendar/body.tpl" dateVar=member_end_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_4}
    </td> 
</tr> 
{if $membershipGroupTree}
<tr>
    <td colspan="4">
    {include file="CRM/Custom/Form/Search.tpl" groupTree=$membershipGroupTree showHideLinks=false}
    </td>
</tr>
{/if}
