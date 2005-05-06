{* Import Wizard - Data Mapping table used by MapFields.tpl and Preview.tpl *}

 <div id="map-field">
    <h4>Import Data -> CiviCRM Contact Fields</h4>
    <table>
        <tr class="columnheader">
            {section name=rows loop=$rowDisplayCount}
		    {if $skipColumnHeader }
                   { if $smarty.section.rows.iteration == 1 }
                     <th>Column Headers</th>
                   {else}
                     <th>Import Data (row {$smarty.section.rows.iteration})</th>
                   {/if}
	        {else}
                  <th>Import Data (row {$smarty.section.rows.iteration})</th>
                {/if}
            {/section}
            
            <th>Matching CiviCRM Field</th>
        </tr>
        
        {*Loop on columns parsed from the import data rows*}
        {section name=cols loop=$columnCount}
            {assign var="i" value=$smarty.section.cols.index}
            <tr>
                         
                {section name=rows loop=$rowDisplayCount}
                    {assign var="j" value=$smarty.section.rows.index}
                    <td class="{if $skipColumnHeader AND $smarty.section.rows.iteration == 1}even-row labels{else}odd-row{/if}">{$dataValues[$j][$i]}</td>
                {/section}

                {* Display mapper <select> field for 'Map Fields', and mapper value for 'Preview' *}
                <td class="form-item even-row{if $wizard.currentStepTitle == 'Preview'} labels{/if}">
                    {if $wizard.currentStepTitle == 'Preview'}
                        {$mapper[$i]}
                    {else}
                        {$form.mapper[$i].html}
                    {/if}
                </td>

            </tr>
        {/section}
                
    </table>
 </div>
