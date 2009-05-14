<div class="form-item">
 <fieldset><legend>{ts}Find Items{/ts}</legend>
  <table class="form-layout">
    <tr>
        <td class="label">{$form.title.label}</td>
        <td>{$form.title.html|crmReplace:class:twenty}
             <div class="description font-italic">
                    {ts}Complete OR partial Item name.{/ts}
             </div>
        </td>
        <td></td>
        <td class="left" colspan="2">{$form.buttons.html}&nbsp;&nbsp;&nbsp;</td>  
    </tr>
    <tr>
        <td class="label">{$form.start_date.label}</td>
        <td>&nbsp;{$form.start_date.html}&nbsp;
            &nbsp;{include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_1}
            {include file="CRM/common/calendar/body.tpl" dateVar=start_date startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_1}
        </td>
        <td class="label">{$form.end_date.label}</td>
        <td>&nbsp;{$form.end_date.html}&nbsp;
            &nbsp;{include file="CRM/common/calendar/desc.tpl" trigger=trigger_search_member_2}
            {include file="CRM/common/calendar/body.tpl" dateVar=end_date startDate=startYear endDate=endYear offset=5 trigger=trigger_search_member_2}
        </td> 
    </tr>
  </table>
</fieldset>
</div>
