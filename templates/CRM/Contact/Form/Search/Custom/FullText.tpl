{* Template for "Sample" custom search component. *}
{assign var="showBlock" value="'searchForm'"}
{assign var="hideBlock" value="'searchForm_show','searchForm_hide'"}

<div id="searchForm_show" class="form-item">
    <a href="#" onclick="hide('searchForm_show'); show('searchForm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}" /></a>
    <label>{ts}Edit Search Criteria{/ts}</label>
</div>

<div id="searchForm" class="form-item">
    <fieldset>
        <legend><span id="searchForm_hide"><a href="#" onclick="hide('searchForm','searchForm_hide'); show('searchForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a></span>{ts}Search Criteria{/ts}</legend>

        <table class="form-layout-compressed">
            {* Loop through all defined search criteria fields (defined in the buildForm() function). *}
            <tr>
                <td class="label">{$form.text.label}</td><td>{$form.text.html}</td>
            </tr>
            <tr>
                <td class="label">{$form.table.label}</td><td>{$form.table.html}</td>
            </tr>
            <tr>
                <td>&nbsp;</td><td>{$form.buttons.html}</td>
            </tr>
        </table>
    </fieldset>
</div>

{if $rowsEmpty}
    {include file="CRM/Contact/Form/Search/Custom/EmptyResults.tpl"}
{/if}

{if !empty($summary.Contact) }
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlock" value="'searchForm_show'"}
    {assign var="hideBlock" value="'searchForm'"}

    <fieldset>
        <legend>Contact</legend>
        {* This section displays the rows along and includes the paging controls *}
        <p>
            {strip}
            <table summary="{ts}Contact listings.{/ts}">
                <tr class="columnheader">
                    <th scope="col">
                        {ts}Name{/ts}
                    </th>
                    <th>&nbsp;</th>
                </tr>

                {foreach from=$summary.Contact item=row}
                    <tr class="{cycle values="odd-row,even-row"}">
                        <td>{$row.display_name}</td>
                        <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{ts}View{/ts}</a></td>
                    </tr>
                {/foreach}
            </table>
            {/strip}
       </p>
    </fieldset>
    {* END Actions/Results section *}
{/if}

{if !empty($summary.Activity) }
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlock" value="'searchForm_show'"}
    {assign var="hideBlock" value="'searchForm'"}

    <fieldset>
        <legend>Activity</legend>
        {* This section displays the rows along and includes the paging controls *}
        <p>
            {strip}
            <table summary="{ts}Contact listings.{/ts}">
                <tr class="columnheader">
                    <th scope="col">{ts}Type{/ts}</th>
                    <th scope="col">{ts}Subject{/ts}</th>
                    <th scope="col">{ts}Details{/ts}</th>
                    <th scope="col">{ts}Source Contact{/ts}</th>
                    <th scope="col">{ts}Target Contact{/ts}</th>
                    <th scope="col">{ts}Assignee Contact{/ts}</th>
                    <th>&nbsp;</th>
                </tr>

                {foreach from=$summary.Activity item=row}
                    <tr class="{cycle values="odd-row,even-row"}">
                        <td>{$row.activity_type}</td>
                        <td>{$row.subject|truncate:40}</td>
                        <td>{$row.details}</td>
                        <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.display_name}</a></td>
                        <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.target_contact_id`"}">{$row.target_display_name}</a></td>
                        <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.assignee_contact_id`"}">{$row.assignee_display_name}</a></td>
                        <td>
                            {if $row.case_id }
                                <a href="{crmURL p='civicrm/case/activity/view' q="reset=1&aid=`$row.activity_id`&cid=`$row.contact_id`&caseID=`$row.case_id`"}">
                            {else}
                                <a href="{crmURL p='civicrm/contact/view/activity' q="atype=`$row.activity_type_id`&action=view&reset=1&id=`$row.activity_id`&cid=`$row.contact_id`"}">
                            {/if}
                        {ts}View{/ts}</a></td>
                    </tr>
                {/foreach}
            </table>
            {/strip}
       </p>
    </fieldset>
    {* END Actions/Results section *}
{/if}

{if !empty($summary.Case) }
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlock" value="'searchForm_show'"}
    {assign var="hideBlock" value="'searchForm'"}

    <fieldset>
        <legend>Case</legend>
        {* This section displays the rows along and includes the paging controls *}
        <p>
            {strip}
            <table summary="{ts}Case listings.{/ts}">
                <tr class="columnheader">
                    <th scope="col">{ts}Client Name{/ts}</th>
                    <th>&nbsp;</th>
                </tr>

                {foreach from=$summary.Case item=row}
                    <tr class="{cycle values="odd-row,even-row"}">
                        <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.display_name}</a></td>
                        <td><a href="{crmURL p='civicrm/contact/view/case' q="reset=1&id=`$row.case_id`&cid=`$row.contact_id`&action=view"}">{ts}View{/ts}</a></td>
                    </tr>
                {/foreach}
            </table>
            {/strip}
       </p>
    </fieldset>
    {* END Actions/Results section *}
{/if}

{if !empty($summary.Contribution) }
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlock" value="'searchForm_show'"}
    {assign var="hideBlock" value="'searchForm'"}

    <fieldset>
        <legend>Contribution</legend>
        {* This section displays the rows along and includes the paging controls *}
        <p>
            {strip}
            <table summary="{ts}Contribution listings.{/ts}">
                <tr class="columnheader">
                    <th scope="col">{ts}Contributor's Name{/ts}</th>
                    <th scope="col">{ts}Amount{/ts}</th>
                    <th scope="col">{ts}Contribution Type{/ts}</th>
                    <th scope="col">{ts}Source{/ts}</th>
                    <th scope="col">{ts}Received{/ts}</th>
                    <th scope="col">{ts}Status{/ts}</th>
                    <th>&nbsp;</th>
                </tr>

                {foreach from=$summary.Contribution item=row}
                    <tr class="{cycle values="odd-row,even-row"}">
                        <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.display_name}</a></td>
                        <td>{$row.contribution_total_amount}</td>
                        <td>{$row.contribution_type}</td>
                        <td>{$row.contribution_source}</td>
                        <td>{$row.contribution_receive_date|crmDate}</td>
                        <td>{$row.contribution_status}</td>
                        <td><a href="{crmURL p='civicrm/contact/view/contribution' q="reset=1&id=`$row.contribution_id`&cid=`$row.contact_id`&action=view"}">{ts}View{/ts}</a></td>
                    </tr>
                {/foreach}
            </table>
            {/strip}
       </p>
    </fieldset>
    {* END Actions/Results section *}
{/if}

{if !empty($summary.Participant) }
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlock" value="'searchForm_show'"}
    {assign var="hideBlock" value="'searchForm'"}

    <fieldset>
        <legend>Participant</legend>
        {* This section displays the rows along and includes the paging controls *}
        <p>
            {strip}
            <table summary="{ts}Participant listings.{/ts}">
                <tr class="columnheader">
                    <th scope="col">{ts}Participant's Name{/ts}</th>
                    <th scope="col">{ts}Event{/ts}</th>
                    <th scope="col">{ts}Fee Level{/ts}</th>
                    <th scope="col">{ts}Fee Amount{/ts}</th>
                    <th scope="col">{ts}Register Date{/ts}</th>
                    <th scope="col">{ts}Source{/ts}</th>
                    <th scope="col">{ts}Status{/ts}</th>
                    <th scope="col">{ts}Role{/ts}</th>
                    <th>&nbsp;</th>
                </tr>

                {foreach from=$summary.Participant item=row}
                    <tr class="{cycle values="odd-row,even-row"}">
                        <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.display_name}</a></td>
                        <td>{$row.event_title}</td>
                        <td>{$row.participant_fee_level}</td>
                        <td>{$row.participant_fee_amount}</td>
                        <td>{$row.participant_register_date|crmDate}</td>
                        <td>{$row.participant_source}</td>
                        <td>{$row.participant_status}</td>
                        <td>{$row.participant_role}</td>
                        <td><a href="{crmURL p='civicrm/contact/view/participant' q="reset=1&id=`$row.participant_id`&cid=`$row.contact_id`&action=view"}">{ts}View{/ts}</a></td>
                    </tr>
                {/foreach}
            </table>
            {/strip}
       </p>
    </fieldset>
    {* END Actions/Results section *}
{/if}

{if !empty($summary.Membership) }
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlock" value="'searchForm_show'"}
    {assign var="hideBlock" value="'searchForm'"}

    <fieldset>
        <legend>Membership</legend>
        {* This section displays the rows along and includes the paging controls *}
        <p>
            {strip}
            <table summary="{ts}Membership listings.{/ts}">
                <tr class="columnheader">
                    <th scope="col">{ts}Member's Name{/ts}</th>
                    <th scope="col">{ts}Membership Type{/ts}</th>
                    <th scope="col">{ts}Membership Fee{/ts}</th>      
                    <th scope="col">{ts}Membership Start Date{/ts}</th>
                    <th scope="col">{ts}Membership End Date{/ts}</th>
                    <th scope="col">{ts}Source{/ts}</th>
                    <th scope="col">{ts}Status{/ts}</th>
                    <th>&nbsp;</th>
                </tr>

                {foreach from=$summary.Membership item=row}
                    <tr class="{cycle values="odd-row,even-row"}">
                        <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.display_name}</a></td>
                        <td>{$row.membership_type}</td>
                        <td>{$row.participant_fee}</td>
                        <td>{$row.membership_start_date|crmDate}</td>
                        <td>{$row.membership_end_date|crmDate}</td>
                        <td>{$row.membership_source}</td>
                        <td>{$row.membership_status}</td>
                        <td><a href="{crmURL p='civicrm/contact/view/membership' q="reset=1&id=`$row.membership_id`&cid=`$row.contact_id`&action=view"}">{ts}View{/ts}</a></td>
                    </tr>
                {/foreach}
            </table>
            {/strip}
       </p>
    </fieldset>
    {* END Actions/Results section *}
{/if}

<script type="text/javascript">
var showBlock = new Array({$showBlock});
var hideBlock = new Array({$hideBlock});

{* hide and display the appropriate blocks *}
on_load_init_blocks( showBlock, hideBlock );
</script>

