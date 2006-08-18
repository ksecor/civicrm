{* template for search builder *}
 <div id="map-field">
  {strip}
     {section start=1 name=blocks loop=$blockCount}
       {assign var="x" value=$smarty.section.blocks.index}
       <fieldset><legend>{if $x eq 1}{ts}Include contacts where{/ts}{else}{ts}Also where{/ts}{/if}</legend>
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
           <td class="form-item even-row underline-effect">
               {$form.addMore[$x].html}
           </td>
         </tr>            
       </table>
      </fieldset>
    {/section}
    <div class="underline-effect">{$form.addBlock.html}</div> 
  {/strip}
 </div>
