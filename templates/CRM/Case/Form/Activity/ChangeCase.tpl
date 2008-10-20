<table class="form-layout">
   <tr><td class="label" width="30%">{$form.case_id.label}</td>
       <td> <div dojoType="dojox.data.QueryReadStore" jsId="caseStore" url="{$caseUrl}" class="tundra">
                                    {$form.case_id.html}
           </div>
       </td>        
   <tr><td class="label">{$form.subject.label}</td><td>{$form.subject.html}</td>        
   <tr><td class="label">{$form.status_id.label}</td><td>{$form.status_id.html}</td>
</table>