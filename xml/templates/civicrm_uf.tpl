INSERT INTO civicrm_uf_group
    (id, is_active, group_type, title, is_cms_user, help_post)
VALUES
    (1, 1, 'Individual,Contact', '{ts}Name and Address{/ts}', 0, null),
    (2, 1, 'Individual,Contact', '{ts}Supporter Profile{/ts}', 2, '<p><strong>{ts}The information you provide will NOT be shared with any third party organisations.{/ts}</strong></p><p>{ts}Thank you for getting involved in our campaign!{/ts}</p>');

INSERT INTO civicrm_uf_join
   (is_active,module,entity_table,entity_id,weight,uf_group_id)
VALUES
   (1, 'User Registration',NULL, NULL,1,1),
   (1, 'User Account', NULL, NULL, 1, 1),
   (1, 'Profile', NULL, NULL, 1, 1),
   (1, 'Profile', NULL, NULL, 2, 2);
   
INSERT INTO civicrm_uf_field
       (`id`, `uf_group_id`, `field_name`, `is_active`, `is_view`, `is_required`, `weight`, `help_post`, `visibility`, `in_selector`, `is_searchable`, `location_type_id`, `phone_type_id`, `label`, `field_type`)
   VALUES
       (1, 1, 'first_name', 1, 0, 1, 1, '', 'Public User Pages and Listings', 0, 1, NULL, NULL, '{ts}First Name{/ts}', 'Individual'),
       (2, 1, 'last_name', 1, 0, 1, 2, '{ts}First and last name will be shared with other visitors to the site.{/ts}', 'Public User Pages and Listings', 0, 1, NULL, NULL, '{ts}Last Name{/ts}', 'Individual'),
       (3, 1, 'street_address', 1, 0, 0, 3, '', 'User and User Admin Only', 0, 0, 1, NULL, '{ts}Street Address (Home){/ts}', 'Contact'),
       (4, 1, 'city', 1, 0, 0, 4, '', 'User and User Admin Only', 0, 0, 1, NULL, '{ts}City (Home){/ts}', 'Contact'),
       (5, 1, 'postal_code', 1, 0, 0, 5, '', 'User and User Admin Only', 0, 0, 1, NULL, '{ts}Postal Code (Home){/ts}', 'Contact'),
       (6, 1, 'country', 1, 0, 0, 6, '{ts}Your state/province and country of residence will be shared with others so folks can find others in their community.{/ts}', 'Public User Pages and Listings', 0, 1, 1, NULL, '{ts}Country (Home){/ts}', 'Contact'),
       (7, 1, 'state_province', 1, 0, 0, 7, '', 'Public User Pages and Listings', 1, 1, 1, NULL, '{ts}State (Home){/ts}', 'Contact'),
       (8, 2, 'first_name', 1, 0, 1, 1, '', 'User and User Admin Only', 0, 0, NULL, NULL, '{ts}First Name{/ts}', 'Individual'),
       (9, 2, 'last_name', 1, 0, 1, 2, '', 'User and User Admin Only', 0, 0, NULL, NULL, '{ts}Last Name{/ts}', 'Individual'),
       (10, 2, 'email', 1, 0, 1, 3, '', 'User and User Admin Only', 0, 0, NULL, NULL, '{ts}Email Address{/ts}', 'Contact');
