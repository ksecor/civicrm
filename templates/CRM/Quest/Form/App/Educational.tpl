{* Quest Pre-application: Educational Interests  section *}

{* Including the javascript source code from the Individual.js *}
 <script type="text/javascript" src="{$config->resourceBase}js/Individual.js"></script>

{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}

<div class="crm-submit-buttons">
    {$form.buttons.html}
</div>	    	
<table cellpadding=0 cellspacing=1 border=0 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.title}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.educational_interest.label}</td>
    <td>
        {$form.educational_interest.html}<br>
        {$form.educational_interest_other.label}{$form.educational_interest_other.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.college_type.label}</td>
    <td>
        {$form.college_type.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.college_interest.label}</td>
    <td>
        {$form.college_interest.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.college_interest_other.label}</td>
    <td>
        {$form.college_interest_other.html}</td>
</tr>
</table>
<div class="crm-submit-buttons">
    {$form.buttons.html}
</div>
