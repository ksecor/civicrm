{* Export Wizard - Data Mapping table used by MapFields.tpl and Preview.tpl *}
 <div id="map-field">
    <p></p>
    <div class="font-size11pt label">{ts}Export Data -&gt; CiviCRM Contact Fields {if $loadedMapping}Using Saved Mapping: {$savedName} {/if} {/ts}</div>
    <br class="spacer"/>

    {if $savedMapping}
    <div>
	<a href="#" onclick="mappingOption(); return false;" > >> Load Saved Field Mapping </a>
    </div>
    <div id="savedMappingOption">
	<span>{$form.savedMapping.label}</span><span>{$form.savedMapping.html}</span>
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
        <tr class="columnheader">
            <th>{ts}Matching CiviCRM Field{/ts}</th>
        </tr>
        {*Loop on columns parsed from the import data rows*}
        {*section name=cols loop=$columnCount*}
        {section name=cols loop=$columnCount}
            {assign var="i" value=$smarty.section.cols.index}
            <tr>
                         
                {section name=rows loop=$rowDisplayCount}
                    {assign var="j" value=$smarty.section.rows.index}
                    <td class="{if $skipColumnHeader AND $smarty.section.rows.iteration == 1}even-row labels{else}odd-row{/if}">{$dataValues[$j][$i]}</td>
                {/section}

                <td class="form-item even-row">
                   {$form.mapper[$i].html}
                </td>
            </tr>
        {/section}
    
        <tr>
           <td class="form-item even-row">
               {$form._qf_MapField_refresh.html}
           </td>
        </tr>            
    </table>
    {/strip}


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

 </div>
