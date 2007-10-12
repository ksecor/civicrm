<div class="form-item">
<fieldset><legend>{ts}Find Event{/ts}</legend>
<table class="form-layout">
    <tr>
        <td>{$form.title.label}
        {$form.title.html}
            <div class="description font-italic">
                {ts}Complete OR partial event name.{/ts}
            </div>
        </td>
        
        <td><label>{ts}Event Type{/ts}</label>
            <div class="listing-box">
                {foreach from=$form.event_type_id item="event_val"}
                <div class="{cycle values="odd-row,even-row"}">
                    {$event_val.html}
                </div>
                {/foreach}
            </div>
        </td>
    
    </tr>
    <tr><td>&nbsp</td></tr>
    <tr>
        <td>&nbsp;{$form.start_date.label}
        
            &nbsp;{$form.start_date.html}&nbsp;<br />
            &nbsp;{include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=start_date startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_1}
        </td>
        <td>{$form.end_date.label}
        
             &nbsp;{$form.end_date.html}<br />
             &nbsp;{include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_2}
             {include file="CRM/common/calendar/body.tpl" dateVar=end_date startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_2}
        </td> 
        <td></td>
    </tr>
</table>
        <div class="right">{$form.buttons.html}</div> 
</fieldset>
</div>