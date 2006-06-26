<dt>&nbsp;</dt>
<dd>
<table class="form-layout">
<tr>
    <td><label>{ts}Membership Type(s){/ts}</label><br />
                <div class="listing-box">
                    {foreach from=$form.membership_type item="membership_type_val"} 
                    <div class="{cycle values="odd-row,even-row"}">
                    {$membership_type_val.html}
                    </div>
                    {/foreach}
                </div>
    </td>
    <td><label>{ts}Membership Status{/ts}</label><br />
                <div class="listing-box">
                    {foreach from=$form.membership_status item="membership_status_val"} 
                    <div class="{cycle values="odd-row,even-row"}">
                    {$membership_status_val.html}
                    </div>
                    {/foreach}
                </div>
    </td>
</tr>
</table>
</dd>

<dt>{$form.source.label}</dt><dd>{$form.source.html}</dd>
<dt>{$form.member_since.label}</dt><dd>{$form.member_since.html}</dd>
<dt>&nbsp;</dt><dd>{include file="CRM/common/calendar/desc.tpl" trigger=trigger1}
             {include file="CRM/common/calendar/body.tpl" dateVar=member_since startDate=startYear endDate=endYear offset=5 trigger=trigger1}</dd>
<dt>{$form.sign_up_from.label}</dt><dd>
<table class="form-layout">
<tr>
    <td>
        {$form.sign_up_from.html}<br/>
        {include file="CRM/common/calendar/desc.tpl" trigger=trigger1}
        {include file="CRM/common/calendar/body.tpl" dateVar=sign_up_from startDate=startYear endDate=endYear offset=5 trigger=trigger1}
    </td>
    <td> 
       {$form.sign_up_to.label} {$form.sign_up_to.html}<br/>
       {include file="CRM/common/calendar/desc.tpl" trigger=trigger2}
       {include file="CRM/common/calendar/body.tpl" dateVar=sign_up_to startDate=startYear endDate=endYear offset=5 trigger=trigger2}
    </td> 
</tr>
</table>
</dd>
<dt>{$form.end_date_from.label}</dt><dd>
<table class="form-layout">
<tr>
    <td>
        {$form.end_date_from.html}<br/>
        {include file="CRM/common/calendar/desc.tpl" trigger=trigger1}
        {include file="CRM/common/calendar/body.tpl" dateVar=sign_up_from startDate=startYear endDate=endYear offset=5 trigger=trigger1}
    </td>
    <td> 
       {$form.end_date_to.label} {$form.end_date_to.html}<br/>
       {include file="CRM/common/calendar/desc.tpl" trigger=trigger2}
       {include file="CRM/common/calendar/body.tpl" dateVar=sign_up_to startDate=startYear endDate=endYear offset=5 trigger=trigger2}
    </td> 
</tr>
</table>
</dd>
</dl>
