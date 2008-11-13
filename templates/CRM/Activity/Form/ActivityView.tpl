<div class="form-item">  
   <fieldset> <legend>{ts}View{/ts} {$activityTypeName}</legend>
      {if $activityTypeDescription}
        <div id="help">{$activityTypeDescription}</div>
      {/if}
      <table class="form-layout">
        <tr>
            <td class="label">{ts}Added By{/ts}</td><td class="view-value">{$values.source_contact}</td>
        </tr>  
        <tr>
            <td class="label">{ts}With Contact{/ts}</td><td class="view-value">{$values.target_contact}</td>
        </tr>  
        <tr>
            <td class="label">{ts}Subject{/ts}</td><td class="view-value">{$values.subject}</td>
        </tr>  
        <tr>
            <td class="label">{ts}Date and Time{/ts}</td><td class="view-value">{$values.activity_date_time|crmDate }</td>
        </tr>  
        <tr>
            <td class="label">{ts}Details{/ts}</td><td class="view-value report">{$values.details}</td>
        </tr>  
{if $values.attachment}
        <tr>
            <td class="label">{ts}Attachment(s){/ts}</td><td class="view-value report">{$values.attachment}</td>
        </tr>  
{/if}
       <tr> 
           <td>&nbsp;</td><td class="buttons">{$form.buttons.html}</td> 
       </tr> 
     </table>
   </fieldset>
</div>  
 
