<div class="form-item">
   <fieldset> <legend>{ts}View{/ts} {$activityTypeName}</legend>
      {if $activityTypeDescription}
        <div id="help">{$activityTypeDescription}</div>
      {/if}
      <table class="form-layout">
        <tr>
            <td class="label">{ts}Added By{/ts}</td><td class="view-value">{$values.source_contact}</td>
        </tr> 
       {if $values.target_contact_value} 
           <tr>
                <td class="label">{ts}With Contact{/ts}</td><td class="view-value">{$values.target_contact_value}</td>
           </tr>
       {/if}
       {if $values.mailingId}
           <tr>
                <td class="label">{ts}With Contact{/ts}</td><td class="view-value"><a href="{$values.mailingId}" title="{ts}View Mailing Report{/ts}">&raquo;{ts}Mailing Report{/ts}</a></td>
           </tr>
       {/if} 
        <tr>
            <td class="label">{ts}Subject{/ts}</td><td class="view-value">{$values.subject}</td>
        </tr>  
        <tr>
            <td class="label">{ts}Date and Time{/ts}</td><td class="view-value">{$values.activity_date_time|crmDate }</td>
        </tr> 
        {if $values.mailingId}
            <tr>
                <td class="label">{ts}Details{/ts}</td>
                <td class="view-value report">
                    
                    <fieldset>
                    <legend>{ts}Content / Components{/ts}</legend>
                    {strip}
                    <table class="form-layout-compressed">
                      {if $mailingReport.mailing.body_text}
                          <tr>
                              <td class="label nowrap">{ts}Text Message{/ts}</td>
                              <td>
                                  {$mailingReport.mailing.body_text|truncate:30|escape|nl2br}
                                  <br />
                                  <strong><a href='{$textViewURL}'>&raquo; {ts}View complete message{/ts}</a></strong>
                              </td>
                          </tr>
                      {/if}

                      {if $mailingReport.mailing.body_html}
                          <tr>
                              <td class="label nowrap">{ts}HTML Message{/ts}</td>
                              <td>
                                  {$mailingReport.mailing.body_html|truncate:30|escape|nl2br}
                                  <br/>                         
                                  <strong><a href='{$htmlViewURL}'>&raquo; {ts}View complete message{/ts}</a></strong>
                              </td>
                          </tr>
                      {/if}

                      {if $mailingReport.mailing.attachment}
                          <tr>
                              <td class="label nowrap">{ts}Attachments{/ts}</td>
                              <td>
                                  {$mailingReport.mailing.attachment}
                              </td>
                              </tr>
                      {/if}
                      
                    </table>
                    {/strip}
                    </fieldset>
                </td>
            </tr>  
        {else}
             <tr>
                 <td class="label">{ts}Details{/ts}</td><td class="view-value report">{$values.details}</td>
             </tr>
        {/if}  
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
 
