{* Partner Athletics Supplements : Grid Table *}
<tr>
    <td colspan=2 class="grouplabel">
    <table cellpadding=0 cellspacing=1 border=2 width="90%" class="app">
       <tr class="tr-vertical-center-text">
          <td><strong>Sport</strong></td>
          <td colspan="4"><strong>Years Played</strong></td>
          <td colspan="2"><strong>Letters</strong></td>
          <td><strong>Event or<br />Position</strong></td>
          <td><strong>Coach</strong></td>
          <td><strong>Varsity Captain</strong></td>
       </tr> 
       <tr class="italic-text">
          <td></td>
          <td>9</td>
          <td>10</td>
          <td>11</td>
          <td>12</td>
          <td>V</td>
          <td>JV</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td></td>
        </tr>

         {section name=rowLoop start=1 loop=8}
             {assign var=i value=$smarty.section.rowLoop.index}
             <tr>
            
             {assign var=activity value="activity_"|cat:$i}
             <td class="fieldlabel">{$form.$activity.html}</td>  
             {section name=columnLoop start=1 loop=7}
                {assign var=j value=$smarty.section.columnLoop.index}
                {assign var=gl value="grade_level_"|cat:$j|cat:"_"|cat:$i}
                <td class="fieldlabel">{$form.$gl.html}</td>
             {/section}

             {assign var=positions value="positions_"|cat:$i}  
             <td class="fieldlabel">{$form.$positions.html|crmReplace:class:eight}</td> 
             {assign var=coaches value="coach_"|cat:$i}  
             <td class="fieldlabel">{$form.$coaches.html|crmReplace:class:eight}</td> 
             {assign var=varsity value="varsity_captain_"|cat:$i}  
             <td class="fieldlabel">{$form.$varsity.html|crmReplace:class:eight}</td> 

             </tr>
          {/section} 
    </table>  
    </td>        
</tr>
