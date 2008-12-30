INSERT INTO civicrm_uf_group
    (id, is_active, group_type, title, is_cms_user, help_post)
VALUES
    (1, 1, 'Individual,Contact', 'Name and Address', 0, null),
    (2, 1, 'Individual,Contact', 'Supporter Profile', 2, '<p><strong>None of the information you are providing will not be made available to any third party organisations.</strong></p><p>Thank you for getting involved in our campaign!</p>');

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
       (1, 1, 'first_name', 1, 0, 1, 1, '', 'Public User Pages and Listings', 0, 1, NULL, NULL, 'First Name', 'Individual'),
       (2, 1, 'last_name', 1, 0, 1, 2, 'First and last name will be shared with other visitors to the site.', 'Public User Pages and Listings', 0, 1, NULL, NULL, 'Last Name', 'Individual'),
       (3, 1, 'street_address', 1, 0, 0, 3, '', 'User and User Admin Only', 0, 0, 1, NULL, 'Street Address (Home)', 'Contact'),
       (4, 1, 'city', 1, 0, 0, 4, '', 'User and User Admin Only', 0, 0, 1, NULL, 'City (Home)', 'Contact'),
       (5, 1, 'postal_code', 1, 0, 0, 5, '', 'User and User Admin Only', 0, 0, 1, NULL, 'Postal Code (Home)', 'Contact'),
       (6, 1, 'state_province', 1, 0, 0, 6, 'Your state/province and country of residence will be shared with others so folks can find others in their community.', 'Public User Pages and Listings', 1, 1, 1, NULL, 'State (Home)', 'Contact'),
       (7, 1, 'country', 1, 0, 0, 7, '', 'Public User Pages and Listings', 0, 1, 1, NULL, 'Country (Home)', 'Contact'),
       (8, 2, 'first_name', 1, 0, 1, 1, '', 'User and User Admin Only', 0, 0, NULL, NULL, 'First Name', 'Individual'),
       (9, 2, 'last_name', 1, 0, 1, 2, '', 'User and User Admin Only', 0, 0, NULL, NULL, 'Last Name', 'Individual'),
       (10, 2, 'email', 1, 0, 1, 3, '', 'User and User Admin Only', 0, 0, NULL, NULL, 'Email Address', 'Contact');
