<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title></title>
</head>
<body>
<center>
  <table width="620" border="0" cellpadding="0" cellspacing="0" id="crm-event_receipt" style="font-family: Arial, Verdana, sans-serif; text-align: left;">
    {* FIXME: move this to a separate template and auto-include here *}
    {if $action eq 1024}{include file="CRM/Contribute/Form/Contribution/ReceiptPreviewHeader.tpl"}{/if}

    <!-- BEGIN HEADER -->
    <!-- You can add table row(s) here with logo or other header elements -->
    <!-- BEGIN HEADER -->

    <!-- BEGIN CONTENT -->
    {if $module eq 'Membership'}
      <tr>
        <td>
          {if $formValues.receipt_text_signup}
            <p>{$formValues.receipt_text_signup}</p>
          {elseif $formValues.receipt_text_renewal}
            <p>{$formValues.receipt_text_renewal}</p>
          {else}
            <p>{ts}Thanks for your support.{/ts}</p>
          {/if}
          {if ! $cancelled}
            <p>{ts}Please print this receipt for your records.{/ts}</p>
          {/if}
        </td>
      </tr>
      <tr>
        <td>
          <table style="border: 1px solid #999; margin: 1em 0em 1em; border-collapse: collapse; width:100%;">
            <tr>
              <th colspan="2" style="text-align: left; padding: 4px; border-bottom: 1px solid #999; background-color: #eee;">
                {ts}Membership Information{/ts}
              </th>
            </tr>
            <tr>
              <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                {ts}Membership Type{/ts}
              </td>
              <td style="padding: 4px; border-bottom: 1px solid #999;">
                {$membership_name}
              </td>
            </tr>
            {if ! $cancelled}
              <tr>
                <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                  {ts}Membership Start Date{/ts}
                </td>
                <td style="padding: 4px; border-bottom: 1px solid #999;">
                  {$mem_start_date}
                </td>
              </tr>
              <tr>
                <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                  {ts}Membership End Date{/ts}
                </td>
                <td style="padding: 4px; border-bottom: 1px solid #999;">
                  {$mem_end_date}
                </td>
              </tr>
              {if $formValues.total_amount}
                <tr>
                  <th colspan="2" style="text-align: left; padding: 4px; border-bottom: 1px solid #999; background-color: #eee;">
                    {ts}Membership Fee{/ts}
                  </th>
                </tr>
                {if $formValues.contributionType_name}
                  <tr>
                    <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                      {ts}Contribution Type{/ts}
                    </td>
                    <td style="padding: 4px; border-bottom: 1px solid #999;">
                      {$formValues.contributionType_name}
                    </td>
                  </tr>
                {/if}
                <tr>
                  <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                    {ts}Amount{/ts}
                  </td>
                  <td style="padding: 4px; border-bottom: 1px solid #999;">
                    {$formValues.total_amount|crmMoney}
                  </td>
                </tr>
                {if $receive_date}
                  <tr>
                    <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                      {ts}Received Date{/ts}
                    </td>
                    <td style="padding: 4px; border-bottom: 1px solid #999;">
                      {$receive_date|truncate:10:''|crmDate}
                    </td>
                  </tr>
                {/if}
                {if $formValues.paidBy}
                  <tr>
                    <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                      {ts}Paid By{/ts}
                    </td>
                    <td style="padding: 4px; border-bottom: 1px solid #999;">
                      {$formValues.paidBy}
                    </td>
                  </tr>
                  {if $formValues.check_number}
                    <tr>
                      <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                        {ts}Check Number{/ts}
                      </td>
                      <td style="padding: 4px; border-bottom: 1px solid #999;">
                        {$formValues.check_number} 
                      </td>
                    </tr>
                  {/if}
                {/if}
              {/if}
            {/if}
          </table>
        </td>
      </tr>
    {elseif $module eq 'Event Registration'}
      <tr>
        <td>
          {if $receipt_text}
            <p>{$receipt_text}</p>
          {/if}
          <p>{ts}Please print this confirmation for your records.{/ts}</p>
        </td>
      </tr>
      <tr>
        <td>
          <table style="border: 1px solid #999; margin: 1em 0em 1em; border-collapse: collapse; width:100%;">
            <tr>
              <th colspan="2" style="text-align: left; padding: 4px; border-bottom: 1px solid #999; background-color: #eee;">
                {ts}Event Information{/ts}
              </th>
            </tr>
            <tr>
              <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                {ts}Event{/ts}
              </td>
              <td style="padding: 4px; border-bottom: 1px solid #999;">
                {$event}
              </td>
            </tr>
            {if $role neq 'Attendee'}
              <tr>
                <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                  {ts}Role{/ts}
                </td>
                <td style="padding: 4px; border-bottom: 1px solid #999;">
                  {$role}
                </td>
              </tr>
            {/if}
            <tr>
              <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                {ts}Registration Date{/ts}
              </td>
              <td style="padding: 4px; border-bottom: 1px solid #999;">
                {$register_date|crmDate}
              </td>
            </tr>
            <tr>
              <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                {ts}Participant Status{/ts}
              </td>
              <td style="padding: 4px; border-bottom: 1px solid #999;">
                {$status}
              </td>
            </tr>
            {if $paid}
              <tr>
                <th colspan="2" style="text-align: left; padding: 4px; border-bottom: 1px solid #999; background-color: #eee;">
                  {ts}Registration Fee{/ts}
                </th>
              </tr>
              <tr>
                <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                  {ts}Amount{/ts}
                </td>
                <td style="padding: 4px; border-bottom: 1px solid #999;">
                  {$total_amount|crmMoney}
                </td>
              </tr>
              {if $receive_date}
                <tr>
                  <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                    {ts}Received Date{/ts}
                  </td>
                  <td style="padding: 4px; border-bottom: 1px solid #999;">
                    {$receive_date|truncate:10:''|crmDate}
                  </td>
                </tr>
              {/if}
              {if $paidBy}
                <tr>
                  <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                    {ts}Paid By{/ts}
                  </td>
                  <td style="padding: 4px; border-bottom: 1px solid #999;">
                    {$paidBy}
                  </td>
                </tr>
              {/if}
            {/if}
          </table>
        </td>
      </tr>
    {/if}

    {if $isPrimary}
      <tr>
        <td>
          <table style="border: 1px solid #999; margin: 1em 0em 1em; border-collapse: collapse; width:100%;">

            {if $contributeMode ne 'notify' and !$isAmountzero and !$is_pay_later  }
              <tr>
                <th colspan="2" style="text-align: left; padding: 4px; border-bottom: 1px solid #999; background-color: #eee;">
                  {ts}Billing Name and Address{/ts}
                </th>
              </tr>
              <tr>
                <td style="padding: 4px; border-bottom: 1px solid #999999;" colspan="2">
                  {$billingName}
                  {$address}
                </td>
              </tr>
            {/if}

            {if $contributeMode eq 'direct' and !$isAmountzero and !$is_pay_later}
              <tr>
                <th colspan="2" style="text-align: left; padding: 4px; border-bottom: 1px solid #999; background-color: #eee;">
                  {ts}Credit Card Information{/ts}
                </th>
              </tr>
              <tr>
                <td style="padding: 4px; border-bottom: 1px solid #999999;" colspan="2">
                  {$credit_card_type}
                  {$credit_card_number}
                </td>
              </tr>
              <tr>
                <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                  {ts}Expires{/ts}
                </td>
                <td style="padding: 4px; border-bottom: 1px solid #999;">
                  {$credit_card_exp_date|truncate:7:''|crmDate}
                </td>
              </tr>
            {/if}

          </table>
        </td>
      </tr>
    {/if}

    {if $customValues}
      <tr>
        <td>
          <table style="border: 1px solid #999; margin: 1em 0em 1em; border-collapse: collapse; width:100%;">
            <tr>
              <th colspan="2" style="text-align: left; padding: 4px; border-bottom: 1px solid #999; background-color: #eee;">
                {$module} {ts}Options{/ts}
              </th>
            </tr>
            {foreach from=$customValues item=value key=customName}
              <tr>
                <td style="padding: 4px; border-bottom: 1px solid #999; background-color: #f7f7f7;">
                  {$customName}
                </td>
                <td style="padding: 4px; border-bottom: 1px solid #999;">
                  {$value}
                </td>
              </tr>
            {/foreach}
          </table>
        </td>
      </tr>
    {/if}

  </table>
</center>
</body>
</html>
