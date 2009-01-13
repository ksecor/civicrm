{* this template is used for adding/editing Payment Reminders Information *}
 <div id="id-paymentReminders" class="section-shown">
    <fieldset>
      <table class="form-layout-compressed">
        <tr><td class="label">{$form.initial_reminder_day.label}</td><td>{$form.initial_reminder_day.html} {help id="id-payment-reminders"}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Days prior to each scheduled payment due date.{/ts}</td></tr>
        <tr><td class="label">{$form.max_reminders.label}</td><td>{$form.max_reminders.html}</td></tr>
        <tr><td class="label">&nbsp;</td><td class="description">{ts}Reminders for each scheduled payment.{/ts}</td></tr>
        <tr><td class="label">{$form.additional_reminder_day.label}</td><td>{$form.additional_reminder_day.html}</td></tr>
	<tr><td class="label">&nbsp;</td><td class="description">{ts}Days after the last one sent, up to the maximum number of reminders.{/ts}</td></tr>
      </table>
   </fieldset>
 </div>     
