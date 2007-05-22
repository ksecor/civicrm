{if $rows}
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlocks" value="'searchForm_show'"}
    {assign var="hideBlocks" value="'searchForm'"}

    {include file="CRM/common/pager.tpl" location="top"}

    <table class="form-layout-compressed">
        <tr>
            <td colspan="2">{$form.toggleSelect.html} <span class="text3">Select All</span></td>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="4" align="center">
            <table class="form-layout">
                <tr height="35">
                    <td><img src="{$zIconURL}save_search_criteria.jpg" border="0" /></td>
                    <td class="text2">Save Search Criteria &nbsp;&nbsp;&nbsp; </td>
                    <td> <img src="{$zIconURL}add_to_database.jpg" border="0" /></td>
                    <td class="text2">Add To Database &nbsp;&nbsp;&nbsp;</td>
                    <td><img src="{$zIconURL}invite_to_network.jpg" border="0" /></td>
                    <td class="text2">Invite to Network	</td>
                </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center" colspan="4"></td>
      </tr>
      {foreach from=$rows key=id item=row}
        <tr>
            {assign var=cbName value=$row.checkbox}
            <td width="20">{$form.$cbName.html}</td>
            <td width="90">
                {if $row.image_URL}
                    <img src="{$row.image_URL}" width="40" height="60" alt="{$row.display_name}" />
                {else}
                    <img src="{$zIconURL}contact_image_not_available.gif" alt="{$row.display_name}" />
                {/if}
            </td>
            <td width="110" style="vertical-align:top">
                <table class="form-layout">
                    <tr><td class="text5">Name:</td></tr>
                    <tr><td class="text5">Status:</td></tr>
                    {if $row.custom_91}<tr><td class="text5">Organization</td></tr>{/if}
                    {if $row.city or $row.state_province or $row.postal_code}
                        <tr><td class="text5">Location:</td></tr>
                    {/if}
                </table>
            </td>
            <td width="270" style="vertical-align:top">
                <table class="form-layout">
                    <tr><td><strong><span class="text7"><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}" title="View this contact.">{$row.display_name}</a></span></strong></td></tr>
                    <tr><td class="text6">{$row.custom_89}</td></tr>
                    {if $row.custom_91}<tr><td class="text6">{$row.custom_91}</td></tr>{/if}
                    {if $row.city or $row.state_province or $row.postal_code}
                        <tr><td class="text6">{$row.city}, {$row.state_province}, {$row.postal_code}</td></tr>
                    {/if}
                </table>
            </td>
            <td width="132" style="vertical-align:top">
                <table class="form-layout" style="margin: 0px; padding: 0px">
                    <tr><td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}" title="View Profile"><img src="{$zIconURL}view_profile.jpg" border="0" /></a></td><td class="text7"><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}" title="View Profile">Action1-Fix Link</a></td></tr>
                    <tr><td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}" title="Add to Database"><img src="{$zIconURL}add_to_database_small.jpg" border="0" /></a></td><td class="text7"><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}" title="Add to Database">Action2-Fix Link</a></td></tr>
                    <tr><td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}" title="Send Message"><img src="{$zIconURL}send_message.jpg" border="0" /></a></td><td class="text7"><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}" title="Send Message">Action3-Fix Link</a></td></tr>
                    <tr><td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}" title="Invite to Network"><img src="{$zIconURL}save_to_network.jpg" border="0" /></a></td><td class="text7"><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}" title="Invite to Network">Action4-Fix Link</a></td></tr>
                </table>
            </td>
        </tr>
      {/foreach}
    </table>
{/if}
