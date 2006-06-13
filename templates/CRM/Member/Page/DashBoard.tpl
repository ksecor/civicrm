{* CiviMember Dashboard (launch page) *}
<div id="help">
    {capture assign=findContactURL}{crmURL p="civicrm/contact/search/basic" q="reset=1"}{/capture}
    {capture assign=importURL}{crmURL p="civicrm/member/import" q="reset=1"}{/capture}
    {capture assign=contribPagesURL}{crmURL p="civicrm/admin/contribute" q="reset=1"}{/capture}
    {capture assign=memberTypesURL}{crmURL p="civicrm/admin/member/membershipType" q="reset=1"}{/capture}
    <p>{ts 1=$contribPagesURL 2=$memberTypesURL}CiviMember allows you to create customized membership types as well as page(s) for online membership sign-up and renewal. Administrators can create or modify Membership Typea <a href="%2">here</a>, and configure Online Contribution Pages which include membership sign-up <a href="%1">here</a>.{/ts}</p>
    <p>{ts 1=$findContactURL 2=$importURL}You can also input and track membership sign-ups offline. To record memberships manually for individual contacts, use <a href="%1">Find Contacts</a> to locate the contact. Then click <strong>View</strong> to go to their summary page and click on the </strong>New Membership</strong> link. You can also <a href="%2">import batches of membership data</a> from other sources.{/ts}</p>
</div>
<hr size="1" noshade/>
<h3>{ts}Membership Summary{/ts}</h3>
<div class="description">
    {capture assign=findMembersURL}{crmURL p="civicrm/member/search/basic" q="reset=1"}{/capture}
    <p>{ts 1=$findMembersURL}This table provides a summary of <strong>Membership Signup and Renewal Activity</strong>, and includes shortcuts to view the details for these commonly used search criteria. To run your own customized searches - click <a href="%1">Find Members</a>. You can search by Member Name, Membership Type, Status, and Signup Date Ranges.{/ts}</p>
</div>
<table class="report form-layout-compressed">
<tr class="columnheader-dark">
    <td>{ts}Members By Type{/ts}</td>
    <td>{ts}June-New/Renew (MTD){/ts}</td>
    <td>{ts}2006-New/Renew (YTD){/ts}</td>
    <td>{ts}Current #{/ts}</td>
</tr>
<tr>
    <td><strong>Gold Level</strong></td>
    <td class="label"><a href="" alt="view details">12</a></td> {* member/search?reset=1&force=1&membership_type_id=1&current=1&start=20060601000000&end=20060612174244 *}
    <td class="label"><a href="" alt="view details">45</a></td> {* member/search?reset=1&force=1&membership_type_id=1&current=1&start=20060101000000&end=20060612174244 *}
    <td class="label"><a href="" alt="view details">125</a></td> {* member/search?reset=1&force=1&membership_type_id=1&current=1 *}
</tr>
<tr>
    <td><strong>Silver Level</strong></td>
    <td class="label"><a href="" alt="view details">9</a></td> {* member/search?reset=1&force=1&membership_type_id=2&current=1&start=20060601000000&end=20060612174244 *}
    <td class="label"><a href="" alt="view details">25</a></td> {* member/search?reset=1&force=1&membership_type_id=2&current=1&start=20060101000000&end=20060612174244 *}
    <td class="label"><a href="" alt="view details">115</a></td> {* member/search?reset=1&force=1&membership_type_id=2&current=1 *}
</tr>
<tr class="columnfooter">
    <td><strong>{ts}Totals (all types){/ts}</strong></a></td>
    <td class="label"><a href="" alt="view details">21</a></td> {* member/search?reset=1&force=1&current=1&start=20060601000000&end=20060612174244 *}
    <td class="label"><a href="" alt="view details">70</a></td> {* member/search?reset=1&force=1&current=1&start=20060101000000&end=20060612174244 *}
    <td class="label"><a href="" alt="view details">240</a></td> {* member/search?reset=1&force=1&current=1 *}
</tr>
</table>
<br />

{* if $pager->_totalItems *}
    <h3>{ts}Recent Memberships{/ts}</h3>
    <div class="form-item">
        <p>(Membership search selector with top 20 membership records by start_date descending goes here.)</p>
        {* include file="CRM/Contribute/Form/Selector.tpl" context="Dashboard" *}
    </div>
{* /if *}
