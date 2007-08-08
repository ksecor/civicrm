{* this template is used for viewing grant *} 
<div class="form-item">
<fieldset>
     <legend>{ts} View Grant{/ts}</legend>
     <dl>
          <dt>{$form.status_id.label}</dt><dd>{$form.status_id.html}</dd>
          <dt>{$form.grant_type_id.label}</dt><dd>{$form.grant_type_id.html}</dd>
          <dt>{ts}Application Recieved {/ts}</dt><dd>{$form.application_received_date.html}
          <dt>{$form.decision_date.label}</dt><dd>{$form.decision_date.html}</dd>
          <dt>{$form.money_transfer_date.label}</dt><dd>{$form.money_transfer_date.html}</dd>
	      <dt>{$form.grant_due_date.label}</dt><dd>{$form.grant_due_date.html}</dd>
	      <dt>{$form.amount_requested.label}</dt><dd>{$form.amount_requested.html}</dd>
          <dt>{$form.amount_granted.label}</dt><dd>{$form.amount_granted.html}</dd>
          <dt>{$form.amount_total.label}</dt><dd>{$form.amount_total.html}</dd>
          <dt>{$form.rationale.label}</dt><dd>{$form.rationale.html}</dd>
          <dt>{$form.note.label}</dt><dd>{$form.note.html}</dd>
          <dt></dt><dd></dd>  
          <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>   
      </dl>
  
    
</fieldset>
</div>        
    