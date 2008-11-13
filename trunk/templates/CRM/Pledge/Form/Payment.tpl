{* this template is used for updating pledge payment*} 
<div class="form-item">
<fieldset><legend>{ts}Edit Pledge Payment{/ts}</legend> 
      <table class="form-layout-compressed">
        <tr><td class="label">{ts}Status{/ts}</td><td class="form-layout">{$status}</td></tr>
        <tr><td class="label">{$form.scheduled_date.label}</td><td>{$form.scheduled_date.html}
            {include file="CRM/common/calendar/desc.tpl" trigger=trigger_pledgepayment}
            {include file="CRM/common/calendar/body.tpl" dateVar=scheduled_date startDate=currentYear endDate=endYear offset=10 trigger=trigger_pledgepayment}
            <br />
            <span class="description">{ts}Scheduled Date for Pledge payment.{/ts}</span></td></tr>
        </td></tr>
      </table>

    <dl>    
       <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>   
    </dl> 
</fieldset>
</div> 
