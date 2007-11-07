<div class="form-item">
 <fieldset><legend>{ts}Find Events{/ts}</legend>
  <table class="form-layout" border=0>
    <tr>
        <td class="label">{$form.title.label}</td>
        <td>{$form.title.html|crmReplace:class:twenty}
             <div class="description font-italic">
                    {ts}Complete OR partial Event name.{/ts}
             </div>
             {$form.eventsByDates.html} 
        </td>
        <td rowspan="1"><label>{ts}Event Type{/ts}</label>
            <div class="listing-box">
                {foreach from=$form.event_type_id item="event_val"}
                <div class="{cycle values="odd-row,even-row"}">
                    {$event_val.html}
                </div>
                {/foreach}
            </div><div class="spacer"></div>
        </td>
        <td class="right" rowspan="1">&nbsp;{$form.buttons.html}</td>  
    </tr>
  
    <tr>
       <td></td>
       <td colspan="3">
       <table class="form-layout" id="id_fromToDates">
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
        </tr>
      </table> 
    </td></tr>  
  </table>
</fieldset>
</div>

{include file="CRM/common/showHide.tpl"}

{literal} 
<script type="text/javascript">
if ( document.getElementsByName('eventsByDates')[1].checked ) {
   show( 'id_fromToDates', 'block' );
}
</script>
{/literal} 