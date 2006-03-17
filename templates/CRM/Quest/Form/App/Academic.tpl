{* Quest Pre-application: Academic Information section *}

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
    <td class="fieldlabel">{$form.gpa.label}</td>
    <td>{$form.gpa.html} <br/> Please give your GPA on an unweighted, 4.0 scale</td>
</tr> 
<tr>
    <td class="fieldlabel">{$form.is_class_ranking.label}</td>
    <td>{$form.is_class_ranking.html}</td>
</tr> 
<tr>
    <td class="fieldlabel">{$form.class_rank.label}</td>
    <td>{$form.class_rank.html}  {$form.class_num_students.html}<br/>Your rank   &nbsp;&nbsp;&nbsp;Total number students in your class</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.class_rank_percent.label}</td>
    <td>{$form.class_rank_percent.html}</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.gpa_explanation.label}</td>
    <td>{$form.gpa_explanation.html}<br/> If there were any extenuating circumstances that affected your GPA, please describe them here.</td>
</tr>
<tr>
    <td>Academic Honors</td>
<tr>
<tr>
    <td>Describe any honors you have been awarded since you entered high school.</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.description_1.label}</td>
    <td>{$form.description_1.html}<br/>honor</td>
</tr>
<tr>
    <td class="fieldlabel">{$form.award_date_1.label}</td>
    <td>{$form.award_date_1.html}</td>
</tr>

</table>
<div class="crm-submit-buttons">
    {$form.buttons.html}
</div>

