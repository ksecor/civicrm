{* Import Wizard - Data Mapping table used by MapFields.tpl and Preview.tpl *}
 <div id="map-field">
    {strip}
    <table class="selector">
    {if $loadedMapping}
        <tr class="columnheader-dark"><th colspan="4">{ts 1=$savedName}Saved Field Mapping: %1{/ts}</td></tr>
    {/if}
        <tr class="columnheader">
	    {if $showColNames}	
	        {assign var="totalRowsDisplay" value=$rowDisplayCount+1}
	    {else}	
	        {assign var="totalRowsDisplay" value=$rowDisplayCount}
	    {/if}	
            {section name=rows loop=$totalRowsDisplay}
                { if $smarty.section.rows.iteration == 1 and $showColNames}
                  <th>{ts}Column Names{/ts}</th>
                {elseif $showColNames}
                  <th>{ts 1=$smarty.section.rows.iteration-1}Import Data (row %1){/ts}</th>
		{else}
		  <th>{ts 1=$smarty.section.rows.iteration}Import Data (row %1){/ts}</th>
                {/if}
            {/section}
            
            <th>{ts}Matching CiviCRM Field{/ts}</th>
        </tr>
        
        {*Loop on columns parsed from the import data rows*}
        {section name=cols loop=$columnCount}
            {assign var="i" value=$smarty.section.cols.index}
            <tr style="border: 1px solid #DDDDDD;">

                {if $showColNames}        
                    <td class="even-row labels">{$columnNames[$i]}</td>
                {/if}

                {section name=rows loop=$rowDisplayCount}
                    {assign var="j" value=$smarty.section.rows.index}
                    <td class="odd-row">{$dataValues[$j][$i]}</td>
                {/section}

                {* Display mapper <select> field for 'Map Fields', and mapper value for 'Preview' *}
                <td class="form-item even-row{if $wizard.currentStepName == 'Preview'} labels{/if}">
                    {if $wizard.currentStepName == 'Preview'}
            			{if $relatedContactDetails && $relatedContactDetails[$i] != ''}
                            {$mapper[$i]} - {$relatedContactDetails[$i]}
                            
                            {if $relatedContactLocType && $relatedContactLocType[$i] != ''}
	                            - {$relatedContactLocType[$i]}
                			{/if}

                            {if $relatedContactPhoneType && $relatedContactPhoneType[$i] != ''}
	                            - {$relatedContactPhoneType[$i]}
                			{/if}
                            
                            {* append IM Service Provider type for related contact *}
                            {if  $relatedContactImProvider && $relatedContactImProvider[$i] != ''}
                                - {$relatedContactImProvider[$i]}
                            {/if}
                                       
			            {else}                        
			                {if $locations[$i]}
                                {$locations[$i]} - 
                            {/if}

                            {if $phones[$i]}
                                {$phones[$i]} - 
                            {/if}
                            
                            {* append IM Service provider type for contact *}
                            {if $ims[$i]}
                                {$ims[$i]} - 
                            {/if}
                            {*else*}
                                {$mapper[$i]}
                            {*/if*}
                        {/if}
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
    		   <dt>{$form.saveMappingDesc.label}</dt><dd>{$form.saveMappingDesc.html}</dd>
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
            cj('select[id^="mapper"][id$="[0]"]').addClass('huge');

	    //Highlight the required field during import
	    paramsArray = new Array();
	    //build the an array of highlighted elements
	    {/literal}
	    {foreach from=$highlightedFields item=paramName}	    
                paramsArray["{$paramName}"] = "1";	    
	    {/foreach}
	    {literal}	             
	    //get select object of first element
	    selObj = document.getElementById("mapper\[0\]\[0\]");   
	    for ( i = 0; i < selObj.options.length; i++ ) {
	        //check value is exist in array
                if (selObj.options[i].value in paramsArray) {
		    //change background Color of all element whose ids start with mapper and end with [0] ;
		    //index value is always some for all select options
                    cj('select[id^="mapper"][id$="[0]"] option:eq('+ i +')').css({"backgroundColor":"#FF9966"});
                }
	    }
            {/literal}	     
	</script>
    </div>
    {/if}
 </div>
