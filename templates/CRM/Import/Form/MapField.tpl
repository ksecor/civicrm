{* Import Wizard - Step 2 (map incoming data fields) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

{* $form.attributes serves as a place holder for all form attributes to be defined in the form tag *}
<form {$form.attributes}>

{* formCommon.tpl handles error msg display, and inserts any required hidden fields. *}
{include file="CRM/formCommon.tpl"}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/WizardHeader.tpl}
 
 <div id="map-field">
 <fieldset><legend>Match Your Data to Contact Fields</legend>
    <table>
        <tr class="columnheader">
            <th>Map to CRM Field</th>
            {section name=rows loop=$rowDisplayCount}
            <th>Your Data (row {$smarty.section.rows.iteration})</th>
            {/section}
        </tr>
        
        {*Loop on columns parsed from the import data rows*}
        {section name=cols loop=$columnCount}
            {assign var="i" value=$smarty.section.cols.index}
            <tr>
                <td class="form-item">
                    {$form.mapper[$i].html}
                </td>
                
                {section name=rows loop=$rowDisplayCount}
                    {assign var="j" value=$smarty.section.rows.index}
                    <td>{$dataValues[$j][$i]}</td>
                {/section}
            </tr>
        {/section}
                
    </table>
 </fieldset>
 </div>

 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
</form>
