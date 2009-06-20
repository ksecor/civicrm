{* tpl file for building email block*}

                                                    <tr id="row0">

                                                      <td style="vertical-align: bottom"><span style="font-size:
                                                    12px;">Email</span>
                                                      &nbsp;
                                                      
						<a style="font-size: 10px; color:#A9290A;"
                                                    href="#"
                                                    id="show-email-2">add</a>                                                   
                                                      
                                                      </td>
                                                      <td align="center"
                                                      style="vertical-align:
                                                      bottom;
                                                      padding-right: 10px;
                                                      padding-left: 10px">On
                                                      Hold?  <img src="../../i/quiz.png" /></td>
                                                      <td align="center" style="vertical-align:
                                                      bottom;
                                                      padding-right: 10px;
                                                      padding-left: 10px">
                                                      Bulk
                                                      Mailings?  <img src="../../i/quiz.png" /></td>                                                      
                                                      <td width="20%"                                                       style="vertical-align:
                                                      bottom;
                                                      padding-right: 10px;
                                                      padding-left: 10px"
                                                    align="center">Primary?</td>                                                      

                                                      <td></td>
                                                    </tr>
                                                    <tr id="email-1" class="last-row">

                                                        <td>

        <input maxlength="64" size="26" name="location[1][email][1][email]" id="location_1_email_1_email"  type="text"
        value="jane@somefoundation.org" >
                                                        
                                                        
        <select name="location[1][location_type_id]" id="location_1_location_type_id" class="form-select">
            <option value="5">Billing</option>
            <option value="1">Home</option>
            <option value="3" selected="selected">Main</option>
            <option value="4">Other</option>
            <option value="2">Work</option>
        </select>
</td>



<td align="center">
        <span><input name="location[1][email][1][on_hold]" type="hidden"><input id="location[1][email][1][on_hold]" name="location[1][email][1][on_hold]" value="1" class="form-checkbox" type="checkbox"></span></td>
        <td align="center">
    <span>&nbsp;<input name="location[1][email][1][is_bulkmail]" value="" type="hidden"><input onchange="email_is_bulkmail_onclick('Edit', 1, 5, 1);" id="location[1][email][1][is_bulkmail]" name="location[1][email][1][is_bulkmail]" value="1" class="form-checkbox" type="checkbox" ></span>
</td>

                                                        <td
                                                    style="vertical-align:
                                                        middle;" align="center"><input
                                                    name="email-primary"
                                                        value=""
                                                    type="radio" checked="checked"></td>

                                               <td></td>         
                                                    </tr>
                                                    <tr id="email-2" style="display:none;">

                                                        <td>

        <input maxlength="64" size="26" name="location[1][email][1][email]" id="location_1_email_1_email"  type="text"
        >
                                                        
                                                        
        <select name="location[1][location_type_id]" id="location_1_location_type_id" class="form-select">
            <option value="5">Billing</option>
            <option value="1" selected="selected">Home</option>
            <option value="3">Main</option>
            <option value="4">Other</option>
            <option value="2">Work</option>
        </select>

</td>


<td align="center">
        <span><input name="location[1][email][1][on_hold]" value="" type="hidden"><input id="location[1][email][1][on_hold]" name="location[1][email][1][on_hold]" value="1" class="form-checkbox" type="checkbox"></span></td>
        <td align="center">
    <span>&nbsp;<input name="location[1][email][1][is_bulkmail]" value="" type="hidden"><input onchange="email_is_bulkmail_onclick('Edit', 1, 5, 1);" id="location[1][email][1][is_bulkmail]" name="location[1][email][1][is_bulkmail]" value="1" class="form-checkbox" type="checkbox"></span>
</td>


                                                        <td align="center"><input
                                                    name="email-primary"
                                                        value=""
                                                    type="radio"></td>

                                               <td><a style="font-size: 10px; color:#A9290A;" href="#" id="hide-email-2">remove</a></td>                                                               
                                                    </tr>
                                                    
                                                    
                                                    

