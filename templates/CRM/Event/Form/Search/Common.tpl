 <tr>
     <td class="label">{ts}Event Name{/ts}</td> 
         <td>
             <script type="text/javascript">
                dojo.require('dojo.widget.ComboBox')
             </script>
             <input type="text" name="title" class="label" value="" dojoType="ComboBox" mode="remote" dataUrl="{$dataURL}" />
             <br />
         </td>
 </tr> 
    
 <tr> 
    <td class="label"> {$form.event_date_low.label} </td>
        <td>
            {$form.event_date_low.html}&nbsp;<br />
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_event_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=event_date_low startDate=startYear endDate=endYear offset=5 trigger=trigger_search_event_1}
        </td>
        <td colspan="2"> 
            {$form.event_date_high.label} {$form.event_date_high.html}<br />
                 &nbsp; &nbsp; {include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_event_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=event_date_high startDate=startYear endDate=endYear offset=5 trigger=trigger_search_event_2}
        </td> 
 </tr>

 <tr>
    <td class="label">{ts}Status{/ts}</td> 
    <td>{$form.participant_status.html}</td>
 </tr> 
    