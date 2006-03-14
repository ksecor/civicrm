{* Quest Pre-application:  essay section *}
{* Including the javascript source code from the Individual.js *}
 <script type="text/javascript" src="{$config->resourceBase}js/Individual.js"></script>

{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}

<div id="help">     
To minimize the risk of losing your work, you may wish to write your essay in another program and then paste it in this box when you are ready.
</div> 

<div class="crm-submit-buttons">
    {$form.buttons.html}
</div>	    	
<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.title}</td>
</tr>
    <tr><td>List and describe the factors in your life that have most shaped you (1500 characters max). *  </td></tr>
    <tr>
      <td> {$form.essay.html}</td>
    </tr>
    
</table>
<div class="crm-submit-buttons">
    {$form.buttons.html}
</div>

