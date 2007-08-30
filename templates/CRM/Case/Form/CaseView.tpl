{* this template is used for adding/editing/deleting case *} 
<div class="form-item">
<fieldset>
    <legend>{ts} View Case Registration{/ts}</legend>
      <dl class="html-adjust">
            <dt class="font-size12pt">{ts}Name{/ts}</dt><dd class="font-size12pt"><strong>{$displayName}</strong>&nbsp;</dd>
      	    <dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
            <dt>{$form.status_id.label}</dt><dd>{$form.status_id.html}</dd>   
            <dt>{$form.casetag1_id.label}</dt><dd>{$form.casetag1_id.html}</dd> 
            <dt>{$form.casetag2_id.label}</dt><dd>{$form.casetag2_id.html}</dd>
            <dt>{$form.casetag3_id.label}</dt><dd>{$form.casetag3_id.html}</dd>
            <dt>{$form.start_date.label}</dt><dd>{$form.start_date.html}</dd>
	        <dt>{$form.end_date.label}</dt><dd>{$form.end_date.html}</dd> 
            <dt>{$form.details.label}</dt><dd>{$form.details.html}</dd>         	 
            <dt></dt><dd>{$form.buttons.html}</dd>
            <dd><a href="{crmURL p='civicrm/contact/view/case' q="reset=1&action=update&cid=`$contactId`&id=`$id`&selectedChild=case&edit=case"}" ">{ts}Edit Case{/ts}</a>&nbsp;|&nbsp;<a href="{crmURL p='civicrm/contact/view/case' q="reset=1&action=delete&cid=`$contactId`&id=`$id`&selectedChild=case&edit=case"}" ">{ts}Remove Case{/ts}</a></dd>
 
    </dl>
    <div class="spacer"> </div>
       <dl><dd>{include file="CRM/History/Selector/Activity.tpl" caseview=1}</dd></dl>
       
</fieldset>
</div>      