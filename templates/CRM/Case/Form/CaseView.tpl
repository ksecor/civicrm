{* this template is used for adding/editing/deleting case *} 
<div class="form-item">
<fieldset>
    <legend>{ts}View Case Record Details{/ts}</legend>
      <dl class="html-adjust">
            <dt class="font-size12pt">{ts}Name{/ts}</dt><dd class="font-size12pt"><strong>{$contactNames}</strong>&nbsp;</dd>
      	    <dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
            <dt>{$form.status_id.label}</dt><dd>{$form.status_id.html}</dd>   
            <dt>{$form.case_type_id.label}</dt><dd>{$form.case_type_id.html}</dd>
            <dt>{$form.start_date.label}</dt><dd>{$form.start_date.html}</dd>
            <dt>{$form.end_date.label}</dt><dd>{$form.end_date.html}</dd> 
            <dt>{$form.details.label}</dt><dd>{$form.details.html}</dd>
      </dl>
  	    <div class="spacer"></div>
            <dd>{include file="CRM/Contact/Page/View/InlineCustomData.tpl"}</dd>
      <dl class="html-adjust">
         <dt></dt><dd>{$form.buttons.html}</dd>	
      </dl>
      <div class="spacer"> </div>
      <dl><dd>{include file="CRM/Activity/Selector/Activity.tpl" caseview=1}</dd></dl>
</fieldset>
</div>      
