
-- Sample Extended Property Group and Fields
INSERT INTO civicrm_custom_group
    (domain_id, name, title, extends, style, collapse_display, help_pre, weight, is_active)
VALUES
    (%%CIVICRM_DOMAIN_ID%%, 'constituent_information', 'Constituent Information', 'Individual', 'Inline', 1, 'Please enter additional constituent information as data becomes available for this contact.', 1, 1);

INSERT INTO civicrm_custom_field
    (custom_group_id, name, label, data_type, html_type, is_required, weight, help_post, is_active, is_searchable, options_per_line)
VALUES
    (1, 'most_important_issue', 'Most Important Issue', 'String', 'Radio', 0, 5, '', 1, 1, NULL),
    (1, 'marital_status', 'Marital Status', 'String', 'Select', 0, 7, '', 1, 1, NULL);

INSERT INTO civicrm_custom_option
    (entity_table,entity_id,label,value,weight,is_active)
VALUES
    ('civicrm_custom_field', 1, 'Education', 'Edu', 1, 1),
    ('civicrm_custom_field', 1, 'Environment', 'Env', 2, 1),
    ('civicrm_custom_field', 1, 'Social Justice', 'SocJus', 3, 1),
    ('civicrm_custom_field', 2, 'Single', 'S', 1, 1),
    ('civicrm_custom_field', 2, 'Married', 'M', 2, 1),
    ('civicrm_custom_field', 2, 'Domestic Partner', 'D', 3, 1),
    ('civicrm_custom_field', 2, 'Widowed', 'W', 4, 1),
    ('civicrm_custom_field', 2, 'Other', 'O', 5, 1),
    ('civicrm_contribution_page',1,'Friend','0.10',1,1),
    ('civicrm_contribution_page',1,'Supporter','0.50',2,1),
    ('civicrm_contribution_page',1,'Booster','1.00',3,1),
    ('civicrm_contribution_page',1,'Sustainer','5.00',4,1);

INSERT INTO civicrm_uf_group
    (domain_id, is_active, form_type, title, help_pre)
VALUES
    (%%CIVICRM_DOMAIN_ID%%, 1, 'CiviCRM Profile', 'Name and Address', '');

INSERT INTO civicrm_uf_join
   (is_active,module,entity_table,entity_id,weight,uf_group_id)
VALUES
   (1,'User Registration','','',1,1),
   (1,'User Account','','',1,1),
   (1,'Profile','','',1,1);

INSERT INTO civicrm_contribution_page
  (domain_id,title,intro_text,contribution_type_id,is_monetary,is_allow_other_amount,default_amount_id,min_amount,max_amount,goal_amount,thankyou_title,thankyou_text,thankyou_footer,receipt_from_name,receipt_from_email,is_email_receipt,cc_receipt,bcc_receipt,receipt_text,is_active,footer_text,amount_block_is_active,is_thermometer,thermometer_title,honor_block_is_active,honor_block_title,honor_block_text)
VALUES
  (%%CIVICRM_DOMAIN_ID%%,'Help Support CiviCRM!','Do you love CiviCRM? Do you use CiviCRM? Then please support CiviCRM and Contribute NOW by trying out our new online contribution features!',1,1,1,9,'10.00','10000.00','100000.00','Thanks for Your Support!','<p>Thank you for your support. Your contribution will help us build even better tools.</p><p>Please tell your friends and colleagues about CiviCRM!</p>','<p><a href=http://civicrm.org>Back to CiviCRM Home Page</a></p>','CiviCRM Fundraising Dept.','donations@civicrm.org',1,'receipt@example.com','bcc@example.com','Your donation is tax deductible under IRS 501(c)(3) regulation. Our tax identification number is: 93-123-4567',1, NULL, 1, 1,'Track Our Progress',NULL, NULL, NULL),
  (%%CIVICRM_DOMAIN_ID%%, 'Member Signup and Renewal', 'Members are the life-blood of our organization. If you''re not already a member - please consider signing up today. You can select the membership level the fits your budget and needs below.', 2, 1, NULL, NULL, NULL, NULL, NULL, 'Thanks for Your Support!', 'Thanks for supporting our organization with your membership. You can learn more about membership benefits from our members only page.', NULL, 'Membership Department', 'memberships@civicrm.org', 1, NULL, NULL, 'Thanks for supporting our organization with your membership. You can learn more about membership benefits from our members only page.\r\n\r\nKeep this receipt for your records.', 1, NULL, NULL, NULL, NULL,NULL, NULL, NULL);
 
INSERT INTO civicrm_contact
    (domain_id, contact_type, contact_sub_type, legal_identifier, external_identifier, sort_name, display_name, nick_name, home_URL, image_URL, source, preferred_communication_method, preferred_mail_format, do_not_phone, do_not_email, do_not_mail, do_not_trade, hash, is_opt_out)
VALUES
    (%%CIVICRM_DOMAIN_ID%%,'Organization',NULL,NULL,NULL,'Inner City Arts','Inner City Arts',NULL,NULL,NULL,NULL,'4','Both',0,0,0,0,'1902067651',0);

INSERT INTO civicrm_organization
    (contact_id, organization_name, legal_name, sic_code, primary_contact_id)
VALUES
    (1,'Inner City Arts',NULL,NULL,NULL);

INSERT INTO civicrm_membership_type
    (domain_id, name, description, member_of_contact_id, contribution_type_id, minimum_fee, duration_unit, duration_interval, period_type, fixed_period_start_day, fixed_period_rollover_day, relationship_type_id, relationship_direction, visibility, weight, is_active)
VALUES
    (%%CIVICRM_DOMAIN_ID%%, 'General', 'Regular annual membership.', 1, 2, 100.00, 'year', 2, 'rolling', NULL, NULL, 7, 'b_a', 'Public', 1, 1),
    (%%CIVICRM_DOMAIN_ID%%, 'Student', 'Discount membership for full-time students.', 1, 1, 50.00, 'year', 1, 'rolling', NULL, NULL, NULL, NULL, 'Public', 2, 1),
    (%%CIVICRM_DOMAIN_ID%%, 'Lifetime', 'Lifetime membership.', 1, 2, 1200.00, 'lifetime', 1, 'rolling', NULL, NULL, 7, 'b_a', 'Admin', 3, 1);

INSERT INTO civicrm_membership_block
    (entity_table, entity_id, membership_types, membership_type_default, display_min_fee, is_separate_payment, new_title, new_text, renewal_title, renewal_text, is_required, is_active)
VALUES
    ('civicrm_contribution_page', 2, '1,2', 1, 1, NULL, 'Membership Levels and Fees', 'Please select the appropriate membership level below. You will have a chance to review your selection and the corresponding dues on the next page prior to your credit card being charged.', 'Renew or Upgrade Your Membership', 'Information on your current membership level and expiration date is shown below. You may renew or upgrade at any time - but don''t let your membership lapse!', 1, 1);


INSERT INTO civicrm_uf_field
    (`id`, `uf_group_id`, `field_name`, `is_active`, `is_view`, `is_required`, `weight`, `help_post`, `visibility`, `in_selector`, `is_searchable`, `location_type_id`, `phone_type`, `label`, `field_type`)
VALUES
    (1, 1, 'first_name', 1, 0, 1, 1, '', 'Public User Pages and Listings', 0, 1, NULL, NULL, 'First Name', 'Individual'),
    (2, 1, 'last_name', 1, 0, 1, 2, 'First and last name will be shared with other visitors to the site.', 'Public User Pages and Listings', 0, 1, NULL, NULL, 'Last Name', 'Individual'),
    (3, 1, 'street_address', 1, 0, 0, 3, '', 'User and User Admin Only', 0, 0, 1, NULL, 'Street Address (Home)', 'Individual'),
    (4, 1, 'city', 1, 0, 0, 4, '', 'User and User Admin Only', 0, 0, 1, NULL, 'City (Home)', 'Individual'),
    (5, 1, 'postal_code', 1, 0, 0, 5, '', 'User and User Admin Only', 0, 0, 1, NULL, 'Postal Code (Home)', 'Individual'),
    (6, 1, 'state_province', 1, 0, 0, 6, 'Your state/province and country of residence will be shared with others so folks can find others in their community.', 'Public User Pages and Listings', 1, 1, 1, NULL, 'State (Home)', 'Individual'),
    (7, 1, 'country', 1, 0, 0, 7, '', 'Public User Pages and Listings', 0, 1, 1, NULL, 'Country (Home)', 'Individual');
        
INSERT INTO civicrm_premiums 
    VALUES (%%CIVICRM_DOMAIN_ID%%, 'civicrm_contribution_page', 1, 1, 'Thank-you Gifts', 'We appreciate your support and invite you to choose from the exciting collection of thank-you gifts below. Minimum contribution amounts for each selection are included in the descriptions. (NOTE: These gifts are shown as examples only. No gifts will be sent to donors.)', 'premiums@example.org', NULL, 1);

INSERT INTO civicrm_product VALUES (1, %%CIVICRM_DOMAIN_ID%%,'Coffee Mug', 'This heavy-duty mug is great for home or office, coffee or tea or hot chocolate. Show your support to family, friends and colleagues. Choose from three great colors.', 'MUG-101', 'White, Black, Green', NULL, NULL, 12.50, 5.00, 2.25, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO civicrm_premiums_product VALUES (1, 1, 1, 1);

-- sample acl entries
-- Create ACL to edit and view contacts in all groups
INSERT INTO civicrm_acl
    ( domain_id, name, deny, object_table, object_id, operation, entity_table, entity_id, is_active )
VALUES
  (%%CIVICRM_DOMAIN_ID%%, 'Edit All Contacts' , 0, 'civicrm_saved_search', 0, 'Edit' , 'civicrm_acl_role', 1, 1 );

-- Create default Groups for User Permissioning
INSERT INTO civicrm_group
(`id`, `domain_id`, `name`, `title`, `description`, `source`, `saved_search_id`, `is_active`, `visibility`, `where_clause`, `select_tables`, `where_tables`)
    VALUES
(1, %%CIVICRM_DOMAIN_ID%%, 'Administrators', 'Administrators', 'Contacts in this group are assigned Administrator role permissions.', NULL, NULL, 1, 'User and User Admin Only', ' ( `civicrm_group_contact-1`.group_id = (1) AND `civicrm_group_contact-1`.status IN ("Added") ) ', 'a:10:{s:15:"civicrm_contact";i:1;s:18:"civicrm_individual";i:1;s:16:"civicrm_location";i:1;s:15:"civicrm_address";i:1;s:22:"civicrm_state_province";i:1;s:15:"civicrm_country";i:1;s:13:"civicrm_email";i:1;s:13:"civicrm_phone";i:1;s:10:"civicrm_im";i:1;s:25:"`civicrm_group_contact-1`";s:114:" LEFT JOIN civicrm_group_contact `civicrm_group_contact-1` ON contact_a.id = `civicrm_group_contact-1`.contact_id ";}', 'a:2:{s:15:"civicrm_contact";i:1;s:25:"`civicrm_group_contact-1`";s:114:" LEFT JOIN civicrm_group_contact `civicrm_group_contact-1` ON contact_a.id = `civicrm_group_contact-1`.contact_id ";}');

-- Assign above Group (entity) to the Administrator Role
INSERT INTO civicrm_acl_entity_role
    (`domain_id`, `acl_role_id`, `entity_table`, `entity_id`, `is_active`)
VALUES
    (%%CIVICRM_DOMAIN_ID%%, 1, 'civicrm_group', 1, 1);

