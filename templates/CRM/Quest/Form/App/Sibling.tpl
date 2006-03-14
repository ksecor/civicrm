{* Quest Pre-application: Sibling Information section *}

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
    <td rowspan=3 valign=top class="grouplabel" width="30%">{ts}Name{/ts}</td>
</tr>    
<tr>
     <td class="fieldlabel">{$form.first_name.html}<br/>
                            {$form.first_name.label}</td>
</tr> 
<tr>
    <td class="fieldlabel"> {$form.last_name.html}<br/>
                            {$form.last_name.label}</td>
</tr> 
<tr>
    <td>{$form.relationship.label}</td>
    <td>{$form.relationship.html}</td>
</tr> 
<tr>
    <td>{$form.age.label}</td>
    <td>{$form.age.html}</td>
</tr>
<tr>
    <td>{$form.lived_with_period_id.label}</td>
    <td>{$form.lived_with_period_id.html}</td>
</tr>
<tr>
    <td>{$form.lived_with_from_age.label}</td>
    <td>{$form.lived_with_from_age.html}</td>
</tr>
<tr>
    <td>{$form.lived_with_to_age.label}</td>
    <td>{$form.lived_with_to_age.html}</td>
</tr>
<tr>
    <td>{$form.current_school_level.label}</td>
    <td>{$form.current_school_level.html}</td>
</tr>
<tr>
    <td>{$form.college_name.label}</td>
    <td>{$form.college_name.html}</td>
<tr>    
    <td>{$form.job_occupation.label}</td>
    <td>{$form.job_occupation.html}</td>
</tr>
<tr>
    <td>{$form.description.label}</td>
    <td>{$form.description.html}</td>
</tr>
</table>
<div class="crm-submit-buttons">
    {$form.buttons.html}
</div>

