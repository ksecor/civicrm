   <tr><td class="label">{$form.case_id.label}</td>
       <td><div dojoType="dojox.data.QueryReadStore" jsId="caseStore" url="{$caseUrl}" class="tundra">
                                    {$form.case_id.html}
           </div>
       </td>
   </tr>        
   <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td></tr>     
