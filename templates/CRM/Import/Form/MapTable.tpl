{* Import Wizard - Data Mapping table used by MapFields.tpl and Preview.tpl *}

 <div id="map-field">
    <h4>{ts}Import Data -&gt; CiviCRM Contact Fields {if $loadedMapping}Using Saved Mapping: {$savedName} {/if} {/ts}</h4>
    {if $savedMapping}
    <div>
	<a href="#" onclick="mappingOption(); return false;" > >> Load Saved Field Mapping </a>
    </div>
    <div id="savedMappingOption">
	<span>{$form.savedMapping.label}</span><span>{$form.savedMapping.html}</span>
	<span>{$form.loadMapping.html}</span>
    </div>
    
    <script type="text/javascript">
	{if $loadedMapping eq ''}
	hide('savedMappingOption');
	document.getElementById("savedMapping").disabled = true;	
	{/if}
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
	
    {if $warning}
	<div class="messages status">
  	<dl>
	    <dt><img src="/drupal/modules/civicrm/i/Inform.gif" alt="status"></dt>
	    <dd>WARNING: The data columns in this import file appear to be different from the saved mapping. Please verify that you have selected the correct saved mapping before continuing.</dd>
	</dl>
	</div>
    {/if}

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
	
	{literal}
	<script type="text/javascript">
	     hide('saveDetails');
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
	</script>
	{/literal}
    </div>
 </div>
