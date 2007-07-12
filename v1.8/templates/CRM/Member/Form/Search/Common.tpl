<tr> <td>&nbsp;</td>
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
    <td class="label">{$form.member_source.label}</td><td>{$form.member_source.html}</td>
    <td colspan="2">{$form.member_test.html}&nbsp;{$form.member_test.label}</td> 
</tr>
{*
<dt>{$form.member_join_date.label}</dt><dd>{$form.member_join_date.html}</dd>
<dt>&nbsp;</dt><dd>{include file="CRM/common/calendar/desc.tpl" trigger=trigger1}
             {include file="CRM/common/calendar/body.tpl" dateVar=member_join_date startDate=startYear endDate=endYear offset=5 trigger=trigger1}</dd>
*}

    <tr> 
            <td class="label"> 
                <br />{$form.member_start_date_low.label} 
            </td>
            <td><br />
                {$form.member_start_date_low.html} &nbsp;<br />
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_1}
                {include file="CRM/common/calendar/body.tpl" dateVar=member_start_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_1}
            </td>
            <td colspan="2"> <br />
                 {$form.member_start_date_high.label} {$form.member_start_date_high.html}<br />
                 &nbsp; &nbsp; {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_2}
                 {include file="CRM/common/calendar/body.tpl" dateVar=member_start_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_2}
            </td> 
    </tr> 
<tr> 
            <td class="label"> <br />
                {$form.member_end_date_low.label} 
            </td>
            <td><br />
                {$form.member_end_date_low.html} &nbsp;<br />
                {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_1}
                {include file="CRM/common/calendar/body.tpl" dateVar=member_end_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_1}
            </td>
            <td colspan="2"> <br />
                 {$form.member_end_date_high.label} {$form.member_end_date_high.html}<br />
                 &nbsp; &nbsp; {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_2}
                 {include file="CRM/common/calendar/body.tpl" dateVar=member_end_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_2}
            </td> 
    </tr> 
</td>

<tr>
            <td colspan="4">
            {include file="CRM/Custom/Form/Search.tpl" groupTree=$membershipGroupTree showHideLinks=false}
            </td>
</tr>
