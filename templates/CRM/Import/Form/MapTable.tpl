{* Import Wizard - Data Mapping table used by MapFields.tpl and Preview.tpl *}

 <div id="map-field">
    <h4>Match Import Data to CiviCRM Contact Fields</h4>
    <table>
        <tr class="columnheader">
            
            {section name=rows loop=$rowDisplayCount}
                <th>Import Data (row {$smarty.section.rows.iteration})</th>
            {/section}
            
            <th>Matching CiviCRM Field</th>
        </tr>
        
        {*Loop on columns parsed from the import data rows*}
        {section name=cols loop=$columnCount}
            {assign var="i" value=$smarty.section.cols.index}
            <tr>
                         
                {section name=rows loop=$rowDisplayCount}
                    {assign var="j" value=$smarty.section.rows.index}
                    <td class="{cycle values="odd-row,even-row"}">{$dataValues[$j][$i]}</td>
                {/section}

                <td class="form-item odd-row">
                    {* Provide mapper <select> field for 'Map Fields', and mapper value for 'Preview' *}
                    {if $wizard.currentStepTitle == 'Match Fields'}
                        {$form.mapper[$i].html}
                    {else}
                        {$mapper[$i]}
                    {/if}
                </td>

            </tr>
        {/section}
                
    </table>
 </div>
