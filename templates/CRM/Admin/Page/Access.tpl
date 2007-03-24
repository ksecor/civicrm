<div id="help">
    <p>{ts 1="http://wiki.civicrm.org/confluence//x/fCM"}ACL&rsquo;s (Access Control Lists) allow you control access to CiviCRM data. An ACL consists of an <strong>Operation</strong>
        (e.g. 'View' or 'Edit'), a <strong>set of Data</strong> that the operation can be performed on (e.g. a group of contacts),
        and a <strong>Role</strong> that has permission to do this operation. Refer to the <a href="%1">ACL Administrator Guide</a> for more info. Note that a CiviCRM ACL Role is not related to the Drupal Role.{/ts}</p>
    <p>{ts}<strong>EXAMPLE:</strong> &quot;Team Leaders&quot; (<em>ACL Role</em>) can &quot;Edit&quot; (<em>Operation</em>) all contacts in the &quot;Active Volunteers Group&quot; (<em>Data</em>).{/ts}</p>
    {if $config->userFramework EQ 'Drupal'}
        <p>{ts 1=$ufAccessURL}Use <a href="%1">Drupal Access Control</a> to manage basic access to CiviCRM components and menu items. Use CiviCRM ACL&rsquo;s to control access to
        specific CiviCRM contact groups. You can also configure ACL&rsquo;s to grant or deny access to specific Profiles, and/or Custom Data Fields.{/ts}</p>
    {elseif $config->userFramework EQ 'Joomla'}
        <p>{ts}ACL&rsquo;s can be used to control access to contacts in CiviCRM "static" or "smart" groups. You can also configure ACL&rsquo;s to grant or deny access to specific Profiles,
         and/or Custom Data Fields.{/ts}</p>
    {/if}
</div>

<table class="report"> 
{if $config->userFramework EQ 'Drupal'}
    <tr>
        <td class="nowrap"><a href="{$ufAccessURL}" id="adminAccess">&raquo; {ts}Drupal Access Control{/ts}</a></td>
        <td>{ts}Grant access to CiviCRM components and access to view or edit contacts using <strong>Drupal roles</strong>.{/ts}</td>
    </tr>
    <tr><td colspan="2" class="separator"><strong>{ts}Use following steps if you need to control View and/or Edit permissions for specific contact groups, specific profiles or specific custom data fields.{/ts}</strong></td></tr>
{/if}
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/admin/options' q="reset=1&group=acl_role"}" id="editACLRoles">&raquo; {ts}1. Manage Roles{/ts}</a></td>
    <td>{ts}Each CiviCRM ACL Role is assigned a set of permissions. Use this link to create or edit the different roles needed for your site.{/ts}</td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/acl' q="reset=1"}" id="editACLs">&raquo; {ts}2. Manage ACL&rsquo;s{/ts}</a></td>
    <td>{ts}ACL&rsquo;s define permission to do an operation on a set of data, and grant that permission to a CiviCRM ACL Role. Use this link to create or edit the ACL&rsquo;s for your site.{/ts}</td>
</tr>
<tr>
    <td class="nowrap"><a href="{crmURL p='civicrm/acl/entityrole' q="reset=1"}" id="editRoleAssignments">&raquo; {ts}3. Assign Users to CiviCRM ACL Roles{/ts}</a></td>
    <td>{ts}Once you have defined CiviCRM ACL Roles and granted ACL&rsquo;s to those Roles, use this link to assign users to role(s).{/ts}</td>
</tr>
</table>