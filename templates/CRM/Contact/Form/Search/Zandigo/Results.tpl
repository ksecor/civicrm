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
        <tr valign="top">
            {assign var=cbName value=$row.checkbox}
            <td width="20">{$form.$cbName.html}</td>
            <td width="90">
                {if $row.image_URL}
                    <img src="{$row.image_URL}" width="40" height="60" alt="{$row.display_name}" />
                {else}
                    <img src="{$zIconURL}contact_image_not_available.gif" alt="{$row.display_name}" />
                {/if}
            </td>
            <td width="110" class="text5">
                Name:<br />
                Status:<br />
                {if $row.custom_91}Organization<br />{/if}
                {if $row.custom_95 or $row.custom_96 or $row.custom_97}Location:{/if}
            </td>
            <td width="270">
                <strong><span class="text7"><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}" title="View this contact.">{$row.display_name}</a></span></strong><br />
                <span class="text6">
                {$row.custom_89}<br/>
                {if $row.custom_91}{$row.custom_91}<br />{/if}
                {if $row.custom_95 or $row.custom_96 or $row.custom_97}
                    {$row.custom_95}, {$row.custom_96}, {$row.custom_97}
                {/if}
                </span>
            </td>
        </tr>
      {/foreach}
    </table>
{/if}
