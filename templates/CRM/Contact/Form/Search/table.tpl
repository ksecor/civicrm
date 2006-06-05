{* template for search builder *}
 <div id="map-field">
  {strip}
   <fieldset><legend>{ts}Include contacts where{/ts}</legend>
    <table >
        {section name=cols loop=$columnCount1}
            {assign var="i" value=$smarty.section.cols.index}
            <tr>
                         
                {section name=rows loop=$rowDisplayCount}
                    {assign var="j" value=$smarty.section.rows.index}
                    <td class="{if $skipColumnHeader AND $smarty.section.rows.iteration == 1}even-row labels{else}odd-row{/if}">{$dataValues[$j][$i]}</td>
                {/section}

                <td class="form-item even-row">
                   {$form.mapper1[$i].html}
	           {$form.operator1[$i].html}
	           &nbsp;&nbsp;{$form.value1[$i].html}
                </td>
            </tr>
        {/section}
    
        <tr>
           <td class="form-item even-row">
               {$form.addMore1.html}
           </td>
        </tr>            
    </table>
   </fieldset>
   <fieldset><legend>{ts}Also include contacts where{/ts}</legend>
    <table >
        {section name=cols loop=$columnCount2}
            {assign var="i" value=$smarty.section.cols.index}
            <tr>
                         
                {section name=rows loop=$rowDisplayCount}
                    {assign var="j" value=$smarty.section.rows.index}
                    <td class="{if $skipColumnHeader AND $smarty.section.rows.iteration == 1}even-row labels{else}odd-row{/if}">{$dataValues[$j][$i]}</td>
                {/section}

                <td class="form-item even-row">
                   {$form.mapper2[$i].html}
	           {$form.operator2[$i].html}
	           &nbsp;&nbsp;{$form.value2[$i].html}
                </td>
            </tr>
        {/section}
    
        <tr>
           <td class="form-item even-row">
               {$form.addMore2.html}
           </td>
        </tr>            
    </table>
   </fieldset>

  {/strip}
 </div>
