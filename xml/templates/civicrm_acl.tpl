-- sample acl entries

-- Create ACL to edit and view contacts in all groups
INSERT INTO civicrm_acl (name, deny, entity_table, entity_id, operation, object_table, object_id, acl_table, acl_id, is_active) 
VALUES 
('Edit All Contacts', 0, 'civicrm_acl_role', 1, 'Edit', 'civicrm_saved_search', 0, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 0, 'All', 'access CiviMail subscribe/unsubscribe pages', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 0, 'All', 'access all custom data', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 0, 'All', 'make online contributions', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 0, 'All', 'make online pledges', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 0, 'All', 'profile listings and forms', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 0, 'All', 'register for events', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'access CiviCRM', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'access CiviContribute', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'access CiviEvent', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'access CiviMail', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'access CiviMail subscribe/unsubscribe pages', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'access CiviMember', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'access CiviPledge', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'access CiviCase', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'access CiviGrant', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'access Contact Dashboard', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'translate CiviCRM', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'edit grants', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'access all custom data', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'access uploaded files', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'add contacts', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'administer CiviCRM', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'edit all contacts', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'edit contributions', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'edit event participants', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'edit groups', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'edit memberships', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'edit pledges', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'import contacts', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'make online contributions', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'make online pledges', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'profile listings and forms', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'register for events', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'view all activities', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'view all contacts', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'view event info', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 1, 'All', 'view event participants', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 2, 'All', 'access CiviMail subscribe/unsubscribe pages', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 2, 'All', 'access all custom data', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 2, 'All', 'make online contributions', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 2, 'All', 'make online pledges', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 2, 'All', 'profile listings and forms', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 2, 'All', 'register for events', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 2, 'All', 'view event info', NULL, NULL, NULL, 1);

-- Create default Groups for User Permissioning
INSERT INTO `civicrm_relationship_type` (`id`, `name_a_b`, `label_a_b`, `name_b_a`,
    `label_b_a`, `description`, `contact_type_a`, `contact_type_b`, `is_reserved`,
    `is_active`, `is_hidden`, `cache_date`, `parents`, `children`, `saved_search_id`,
    `visibility`)
VALUES (1,'Administrators','Administrators','Administrated by','Administrated by',
    'System Administrators','Individual','Organization',1,1,0,NULL,NULL,NULL,NULL,
    'User and User Admin Only');

-- Assign above Group (entity) to the Administrator Role
INSERT INTO civicrm_acl_entity_role
    (`acl_role_id`, `entity_table`, `entity_id`, `is_active`)
VALUES
    (1, 'civicrm_relationship_type', 1, 1);

