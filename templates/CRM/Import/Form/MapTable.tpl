{* Import Wizard - Data Mapping table used by MapFields.tpl and Preview.tpl *}

 <div id="map-field">
    <h4>{ts}Import Data -&gt; CiviCRM Contact Fields{/ts}</h4>
    <table>
        <tr class="columnheader">
            {section name=rows loop=$rowDisplayCount}
		    {if $skipColumnHeader }
                   { if $smarty.section.rows.iteration == 1 }
                     <th>Column Headers</th>
                   {else}
                     <th>{ts 1=$smarty.section.rows.iteration}Import Data (row %1){/ts}</th>
                   {/if}
	        {else}
                  <th>{ts 1=$smarty.section.rows.iteration}Import Data (row %1){/ts}</th>
                {/if}
            {/section}
            
            <th>{ts}Matching CiviCRM Field{/ts}</th>
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
                        {if $locations[$i]}
                            {$locations[$i]}
                        {/if}
                        {if $phones[$i]}
                            {$phones[$i]}
                        {else}
                            {$mapper[$i]}
                        {/if}
                    {else}
                        {$form.mapper[$i].html}
                    {/if}
                </td>

            </tr>
        {/section}
                
    </table>
 </div>
