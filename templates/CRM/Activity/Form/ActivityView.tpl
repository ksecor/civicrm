<div class="form-item">  
<div id="help">{$activityTypeDescription}</div>
   <fieldset> <legend>{ts}View{/ts} {$activityTypeName}</legend>
      <table class="form-layout">
        <tr>
            <td class="label">{ts}Added By {/ts}</td><td>{$values.source_contact}</td>
        </tr>  
        <tr>
            <td class="label">{ts}With Contact{/ts}</td><td>{$values.target_contact}</td>
        </tr>  
        <tr>
            <td class="label">{ts}Subject{/ts}</td><td>{$values.subject}</td>
        </tr>  
        <tr>
            <td class="label">{ts}Date and Time{/ts}</td><td>{$values.activity_date_time | crmDate }</td>
        </tr>  
        <tr>
            <td class="label">{ts}Details{/ts}</td><td>{$values.details}</td>
        </tr>  
       <tr> 
           <td>&nbsp;</td><td>{$form.buttons.html}</td> 
       </tr> 
     </table>
   </fieldset>
</div>  
 
