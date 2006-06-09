{* Activity History Import Wizard - Data Mapping table used by MapFields.tpl and Preview.tpl *}

 <div id="map-field">
    {if $savedMapping}
    <div>
	<p><a href="#" onclick="mappingOption(); return false;" >&raquo; {if $loadedMapping}{ts}Select a Different Field Mapping{/ts}{else}{ts}Load Saved Field Mapping{/ts}{/if}</a></p>
    </div>
    <div id="savedMappingOption">
	<span>{$form.savedMapping.label}</span> <span>{$form.savedMapping.html}</span>
	<span>{$form.loadMapping.html}</span>
    </div>
    
    <script type="text/javascript">
	hide('savedMappingOption');
	document.getElementById("savedMapping").disabled = true;	
	{literal}
	function mappingOption() {
		if (document.getElementById("savedMappingOption").style.display == "block") {
		    hide('savedMappingOption');
		    document.getElementById("savedMapping").disabled = true;
		    return false;
		} else {
		    show('savedMappingOption');
		    document.getElementById("savedMapping").disabled = false;
		    return false;
		}
	}
		
	{/literal}
    </script>  
    {/if}

    {strip}
    <table>
    {if $loadedMapping} 
        <tr class="columnheader-dark"><th colspan="4">{ts 1=$savedName}Saved Field Mapping: %1{/ts}</td></tr>
    {/if}
        <tr class="columnheader">
            {section name=rows loop=$rowDisplayCount}
		   {if $skipColumnHeader }
                   { if $smarty.section.rows.iteration == 1 }
                     <th>{ts}Column Headers{/ts}</th>
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
                <td class="form-item even-row
                    {if $wizard.currentStepName == 'Preview'}labels{/if}">
                    {if $wizard.currentStepName == 'Preview'}
                        {$mapper[$i]}
                    {else}
                        {$form.mapper[$i].html}
                    {/if}
                </td>

            </tr>
        {/section}
                
    </table>
	{/strip}

    {if $wizard.currentStepName != 'Preview'}
    <div>
    
    	{if $loadedMapping} 
        	<span>{$form.updateMapping.html} &nbsp;&nbsp; {$form.updateMapping.label}</span>
    	{/if}
    	<span>{$form.saveMapping.html} &nbsp;&nbsp; {$form.saveMapping.label}</span>
    	<div id="saveDetails" class="form-item">
    	      <dl>
    		   <dt>{$form.saveMappingName.label}</dt><dd>{$form.saveMappingName.html}</dd>
    		   <dt>{$form.saveMappingDesc.label}</dt><dd>{$form.saveMappingDesc.html}<dd>
    	      </dl>
    	</div>
    	<script type="text/javascript">
             {if $mappingDetailsError }
                show('saveDetails');    
             {else}
        	    hide('saveDetails');
             {/if}
    
    	     {literal}   
 	         function showSaveDetails(chkbox) {
        		 if (chkbox.checked) {
        			document.getElementById("saveDetails").style.display = "block";
        			document.getElementById("saveMappingName").disabled = false;
        			document.getElementById("saveMappingDesc").disabled = false;
        		 } else {
        			document.getElementById("saveDetails").style.display = "none";
        			document.getElementById("saveMappingName").disabled = true;
        			document.getElementById("saveMappingDesc").disabled = true;
        		 }
             }
             {/literal}	     
	    </script>
    </div>
    {/if}
 </div>
