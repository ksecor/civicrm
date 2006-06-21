{* template for search builder *}
 <div id="map-field">
  {strip}
     {section start=1 name=blocks loop=$blockCount}
       {assign var="x" value=$smarty.section.blocks.index}
       <fieldset><legend>{ts}{if $x eq 1}Include contacts where{else}Also where{/if}{/ts}</legend>
	<table>
        {section name=cols loop=$columnCount[$x]}
            {assign var="i" value=$smarty.section.cols.index}
            <tr>
                         
                <td class="form-item even-row">
                   {$form.mapper[$x][$i].html}
	           {$form.operator[$x][$i].html}
	           &nbsp;&nbsp;{$form.value[$x][$i].html}
                </td>
            </tr>
        {/section}
    
         <tr>
           <td class="form-item even-row">
               {$form.addMore[$x].html}
           </td>
         </tr>            
       </table>
      </fieldset>
    {/section}
    <span>{$form.addBlock.html}</span> 
  {/strip}
 </div>
