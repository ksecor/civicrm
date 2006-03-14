{* Quest Pre-application: Scholarship Information section *}

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
     <td class="fieldlabel"> {$form.internet_access.label} <span class="marker">*</span> </td>
     <td>{$form.internet_access.html}</td>
</tr> 
<tr>
    <td class="fieldlabel"> {$form.is_home_computer.label}<span class="marker">*</span> </td>
    <td> {$form.is_home_computer.html} </td>
</tr> 
<tr>
    <td class="fieldlabel"> {$form.is_home_internet.label} </td>
    <td> {$form.is_home_internet.html} </td>
</tr> 
<tr>
    <td class="fieldlabel"> {$form.is_take_SAT_ACT.label}<span class="marker">*</span> </td>
    <td> {$form.is_take_SAT_ACT.html} </td>
</tr>
<tr>
    <td class="fieldlabel"> {$form.study_method.label}</td>
    <td>{$form.study_method.html}</td>
</tr>
</table>
<div class="crm-submit-buttons">
    {$form.buttons.html}
</div>

