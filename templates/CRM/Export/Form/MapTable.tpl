{* Export Wizard - Data Mapping table used by MapFields.tpl and Preview.tpl *}
 <div id="map-field">
    <p></p>
    <div class="font-size11pt label">{ts}Export Data -&gt; CiviCRM Contact Fields{/ts}</div>
    <br class="spacer"/>
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
           <td>
              <a href="{crmURL p='civicrm/export/contact/' q="_qf_MapField_display=true&more=true"}">Give me more columns</a>
           </td>
        </tr>            
    </table>
    {/strip}
 </div>
