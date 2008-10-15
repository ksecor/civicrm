Currently, this module simply examines a user's roles upon account creation, update, or login, and if there exists a CiviCRM group with the same name as the a role possessed by the user, the user will be added to that CiviCRM group. 

In short, this is one way synchronization, from Drupal Roles -> CiviCRM Groups, but only if the Group already exists.

Note that for now, removing from a Role does NOT remove the user from the CiviCRM group, so be sure to remove them manually if you need to do so.

Author: Matt Chapman

For support & feature requests, please visit http://forum.civicrm.org/
