{* this template is used for updating pledge payment*} 
<div class="form-item">
<fieldset><legend>{ts}Edit Pledge Payment{/ts}</legend> 
      <table class="form-layout-compressed">
        <tr><td class="label">{ts}Status{/ts}</td><td class="form-layout">{$status}</td></tr>
        <tr><td class="label">{$form.scheduled_date.label}</td>
            <td>{include file="CRM/common/jcalendar.tpl" elementName=scheduled_date}
            <span class="description">{ts}Scheduled Date for Pledge payment.{/ts}</span></td></tr>
        </td></tr>
      </table>

    <dl>    
       <dt></dt><dd class="html-adjust">{$form.buttons.html}</dd>   
    </dl> 
</fieldset>
</div> 
