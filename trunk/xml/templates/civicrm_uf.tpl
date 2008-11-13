INSERT INTO civicrm_uf_group
    (is_active, group_type, title, help_pre)
VALUES
    (1, 'Individual,Contact', 'Name and Address', '');

INSERT INTO civicrm_uf_join
   (is_active,module,entity_table,entity_id,weight,uf_group_id)
VALUES
   (1,'User Registration','',NULL,1,1),
   (1,'User Account','',NULL,1,1),
   (1,'Profile','',NULL,1,1);
   
INSERT INTO civicrm_uf_field
       (`id`, `uf_group_id`, `field_name`, `is_active`, `is_view`, `is_required`, `weight`, `help_post`, `visibility`, `in_selector`, `is_searchable`, `location_type_id`, `phone_type_id`, `label`, `field_type`)
   VALUES
       (1, 1, 'first_name', 1, 0, 1, 1, '', 'Public User Pages and Listings', 0, 1, NULL, NULL, 'First Name', 'Individual'),
       (2, 1, 'last_name', 1, 0, 1, 2, 'First and last name will be shared with other visitors to the site.', 'Public User Pages and Listings', 0, 1, NULL, NULL, 'Last Name', 'Individual'),
       (3, 1, 'street_address', 1, 0, 0, 3, '', 'User and User Admin Only', 0, 0, 1, NULL, 'Street Address (Home)', 'Contact'),
       (4, 1, 'city', 1, 0, 0, 4, '', 'User and User Admin Only', 0, 0, 1, NULL, 'City (Home)', 'Contact'),
       (5, 1, 'postal_code', 1, 0, 0, 5, '', 'User and User Admin Only', 0, 0, 1, NULL, 'Postal Code (Home)', 'Contact'),
       (6, 1, 'state_province', 1, 0, 0, 6, 'Your state/province and country of residence will be shared with others so folks can find others in their community.', 'Public User Pages and Listings', 1, 1, 1, NULL, 'State (Home)', 'Contact'),
       (7, 1, 'country', 1, 0, 0, 7, '', 'Public User Pages and Listings', 0, 1, 1, NULL, 'Country (Home)', 'Contact');