This module provides two-way synchronization between Drupal Roles and CiviCRM based on rules defined by the administrator.

Note that for now, removing from a Role does NOT remove the user from the CiviCRM group, so be sure to remove them manually from the group if you need to do so.

Also, removing a Contact from a Group will remove the user from any Roles synchronized with that Group, even if the contact remains in other groups that would normally grant the role.

Original Author: Matt Chapman - http://drupal.org/user/143172

For support & feature requests, please visit http://forum.civicrm.org/