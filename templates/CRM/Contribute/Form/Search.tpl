<fieldset>
    <div class="form-item">                    
    {strip} 
        <table class="form-layout"> 
        <tr>                                          
            <td class="font-size12pt">{$form.sort_name.label}</td> 
            <td>{$form.sort_name.html}    
                <div class="description font-italic"> 
                    {ts}Complete OR partial contact name OR email.{/ts} 
                </div>
            </td> 
            <td class="label">{$form.buttons.html}</td>        
        </tr>
        <tr> 
            <td><label>{ts}Contribution Type{/ts}</label><br /> 
                {$form.contribution_type_id.html} 
            </td> 
            <td><label>{ts}Payment Instrument{/ts}</label><br /> 
                {$form.payment_instrument_id.html} 
            </td> 
            <td><label>{ts}Contribution Status{/ts}</label><br /> 
                {$form.contribution_status.html} 
            </td>
        </tr>
        <tr> 
            <td class="label"> 
                {$form.contribution_from_date.label} 
            </td> 
            <td> 
                 {$form.contribution_from_date.html} &nbsp; {$form.contribution_to_date.label} {$form.contribution_to_date.html} 
            </td> 
        </tr> 
        <tr> 
            <td class="label"> 
                {$form.contribution_min_amount.label} 
            </td> 
            <td> 
                 {$form.contribution_min_amount.html} &nbsp; {$form.contribution_max_amount.label} {$form.contribution_max_amount.html} 
            </td> 
        </tr> 
        </table>
    {/strip}
    </div>
</fieldset>

{include file="CRM/pager.tpl" location="top"}

{if ! empty( $rows )}

{strip}
<table>
  <tr class="columnheader">
  <th>{$form.toggleSelect.html}</th> 
  {foreach from=$columnHeaders item=header}
    <th>
    {if $header.sort}
      {assign var='key' value=$header.sort}
      {$sort->_response.$key.link}
    {else}
      {$header.name}
    {/if}
    </th>
  {/foreach}
  </tr>

  {counter start=0 skip=1 print=false}
  {foreach from=$rows item=row}
  <tr id='rowid{$row.contact_id}' class="{cycle values="odd-row,even-row"}">
    {assign var=cbName value=$row.checkbox}
    <td>{$form.$cbName.html}</td> 
    <td>{$row.contact_type}</td>	
    <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
    <td>{$row.total_amount}</td>
    <td>{$row.contribution_type}</td>
    <td>{$row.contribution_source}</td>
    <td>{$row.receive_date}</td>
    <td>{$row.thankyou_date}</td>
    <td>{$row.cancel_date}</td>
    <td>{$row.action}</td>
  </tr>
  {/foreach}
  <tr></tr>
  <tr>
    <td></td>
    <td>Totals</td>
    <td>{$num_amount}</td>
    <td>{$total_amount}</td>
    <td>{$cancel_amount}</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
{/strip}

 <script type="text/javascript">
 {* this function is called to change the color of selected row(s) *}
    var fname = "{$form.formName}";	
    on_load_init_checkboxes(fname);
 </script>


{include file="CRM/pager.tpl" location="bottom"}

{/if}
