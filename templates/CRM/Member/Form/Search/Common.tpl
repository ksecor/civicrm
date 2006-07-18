{strip}
<dl>
<dt>&nbsp;</dt>
<dd>
<table class="form-layout">
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
</table>
</dd>

<dt>{$form.member_source.label}</dt><dd>{$form.member_source.html}</dd>
{*
<dt>{$form.member_join_date.label}</dt><dd>{$form.member_join_date.html}</dd>
<dt>&nbsp;</dt><dd>{include file="CRM/common/calendar/desc.tpl" trigger=trigger1}
             {include file="CRM/common/calendar/body.tpl" dateVar=member_join_date startDate=startYear endDate=endYear offset=5 trigger=trigger1}</dd>
*}
<dt>{$form.member_start_date_low.label}</dt><dd>
<table class="form-layout">
<tr>
    <td>
        {$form.member_start_date_low.html}<br/>
        {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_1}
        {include file="CRM/common/calendar/body.tpl" dateVar=member_start_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_1}
    </td>
    <td> 
       {$form.member_start_date_high.label} {$form.member_start_date_high.html}<br/>
       {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_2}
       {include file="CRM/common/calendar/body.tpl" dateVar=member_start_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_2}
    </td> 
</tr>
</table>
</dd>
<dt>{$form.member_end_date_low.label}</dt><dd>
<table class="form-layout">
<tr>
    <td>
        {$form.member_end_date_low.html}<br/>
        {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_3}
        {include file="CRM/common/calendar/body.tpl" dateVar=member_end_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_3}
    </td>
    <td> 
       {$form.member_end_date_high.label} {$form.member_end_date_high.html}<br/>
       {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_4}
       {include file="CRM/common/calendar/body.tpl" dateVar=member_end_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_4}
    </td> 
</tr>
</table>
</dd>
</dl>
{/strip}
