{* tpl for building phone related fields*}

<tr id="row0">

  <td style="
  vertical-align:
bottom"
><span style="font-size:
12px;">Phone</span>
  &nbsp;
  
<a style="font-size: 10px; color:#A9290A;"
href="#"
onclick="cj('#row3').show();cj('#show2').hide();return
false;">add</a>                                                      
  
  </td>
  <td colspan="2"></td>
  <td
  align="center">Primary?</td>                                                                                                            
</tr>
                                                    <tr class="last-row">


                                                        <td>

   <input maxlength="64" size="26" name="location[1][phone][1][phone]" id="location_1_phone_1_phone" type="text">

        <select name="location[1][location_type_id]" id="location_1_location_type_id" class="form-select">
            <option value="5">Billing</option>
            <option value="1" selected="selected">Home</option>
            <option value="3">Main</option>
            <option value="4">Other</option>
            <option value="2">Work</option>
        </select>
        


</td>

<td colspan="2">

        <select name="location[1][phone][1][phone_type_id]" id="location_1_phone_1_phone_type_id" class="form-select">

             <option value="">- select -</option>
             <option value="1" selected="selected">Phone</option>
             <option value="2">Mobile</option>
             <option value="3">Fax</option>
             <option value="4">Pager</option>
             <option value="5">Voicemail</option>
         </select>        
                                                        
</td>


                                                        <td
                                                    style="vertical-align:
                                                        middle;" align="center"><input
                                                    name="phone-primary"
                                                        value=""
                                                    checked="checked"
                                                        disabled="disabled"
                                                    type="radio"></td>

                                               <td></td>         
                                                    </tr>

