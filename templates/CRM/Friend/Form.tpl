{if $status eq 'thankyou' }
{$thankYouText}
{else}

<table class="form-layout-compressed" >
   <tr><td colspan=2>{$intro}</td></tr>

   <tr><td>{$form.from_name.label}</td><td>{$form.from_name.html}</td></tr>
   <tr><td>{$form.from_email.label}</td><td>{$form.from_email.html}</td></tr>     
   <tr><td>{$form.suggested_message.label}</td><td>{$form.suggested_message.html}</td></tr>

   <tr><td></td><td>  
    <fieldset><legend>{ts}Friend Detail(s){/ts}</legend>  
    <table >
      <tr class="columnheader" >
          <td>{ts}First Name(s){/ts}</td>
          <td>{ts}Last Name(s){/ts}</td>
          <td>{ts}Email Address(es){/ts}</td>
      </tr>
    {section name=loop start=1 loop=4}
        {assign var=idx value=$smarty.section.loop.index}
         <tr>
            <td class="even-row">{$form.friend.$idx.first_name.html}</td>
            <td class="even-row">{$form.friend.$idx.last_name.html}</td>
	    <td class="even-row">{$form.friend.$idx.email.html}</td>
         </tr>
    {/section}      
    </table>
    </fieldset>
    </td>
   </tr>
   <tr><td>&nbsp;</td><td>{$form.buttons.html}</td></tr>	
 </table>      
{/if}