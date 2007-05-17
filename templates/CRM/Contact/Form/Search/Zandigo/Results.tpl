{if $rows}
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlocks" value="'searchForm_show'"}
    {assign var="hideBlocks" value="'searchForm'"}

    {include file="CRM/common/pager.tpl" location="top"}

    <table class="form-layout-compressed">
    {foreach from=$rows key=id item=row}
        <tr>
            {assign var=cbName value=$row.checkbox}
            <td>{$form.$cbName.html}</td>
            <td><img src="{$row.image_URL}" width="40" height="60" alt="{$row.display_name}" /></td>
            <td>
                Name:<br />
                Status:<br />
                {if $row.custom_91}Organization<br />{/if}
                {if $row.custom_95 or $row.custom_96 or $row.custom_97}Location:{/if}
            </td>
            <td>
                <strong>{$row.display_name}</strong><br />
                {$row.custom_89}<br/>
                {if $row.custom_91}{$row.custom_91}<br />{/if}
                {if $row.custom_95 or $row.custom_96 or $row.custom_97}
                    {$row.custom_95}, {$row.custom_96}, {$row.custom_97}
                {/if}
            </td>
        </tr>
    {/foreach}
    </table>
{/if}
