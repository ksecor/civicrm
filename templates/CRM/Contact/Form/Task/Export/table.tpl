{* Export Wizard - Data Mapping table used by MapFields.tpl and Preview.tpl *}
 <div id="map-field">
    {if $savedMapping}
    <div>
	<a href="#" onclick="mappingOption(); return false;" >&raquo; {if $loadedMapping}{ts}Select a Different Mapping{/ts}{else}{ts}Load Saved Field Mapping{/ts}{/if}</a>
    </div>
    <div id="savedMappingOption">
	<span>{$form.savedMapping.label}</span>&nbsp;<span>{$form.savedMapping.html}</span>
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
            <tr class="columnheader-dark"><th colspan="4">{ts 1=$savedName}Using Field Mapping: %1{/ts}</td></tr>
        {/if}
        <tr class="columnheader">
            <th>{ts}Fields to Include in Export File{/ts}</th>
        </tr>
        {*section name=cols loop=$columnCount*}
        {section name=cols loop=$columnCount.1}
            {assign var="i" value=$smarty.section.cols.index}
            <tr>
                <td class="form-item even-row">
                   {$form.mapper.1[$i].html}
                </td>
            </tr>
        {/section}
    
        <tr>
           <td class="form-item even-row underline-effect">
               {$form.addMore.1.html}
           </td>
        </tr>            
    </table>
    {/strip}


    <div>
	{if $loadedMapping}
<span>{$form.updateMapping.html}{$form.updateMapping.label}&nbsp;&nbsp;&nbsp;</span>
	{/if}
	<span>{$form.saveMapping.html}{$form.saveMapping.label}</span>
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
         {/literal}	     
	</script>
    </div>

 </div>
