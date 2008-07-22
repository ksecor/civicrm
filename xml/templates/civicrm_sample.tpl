
-- Sample Extended Property Group and Fields

INSERT INTO 
   `civicrm_option_group` (`name`, `description`, `is_reserved`, `is_active`) 
VALUES 
    ('civicrm_contribution_page.amount.1', 'Contribution Page Amount: 1', 0, 1);

SELECT @option_cpage_id   := max(id) from civicrm_option_group where name = 'civicrm_contribution_page.amount.1';

INSERT INTO 
   `civicrm_option_value` (`option_group_id`, `label`, `value`, `weight`, `is_active`, `is_default`) 
VALUES
    (@option_cpage_id,   'Friend','1.00',1,1,0),
    (@option_cpage_id,   'Supporter','5.00',2,1,0),
    (@option_cpage_id,   'Booster','10.00',3,1,1),
    (@option_cpage_id,   'Sustainer','50.00',4,1,0);
    
INSERT INTO civicrm_contribution_page
  (title,intro_text,contribution_type_id,is_monetary,is_allow_other_amount,default_amount_id,min_amount,max_amount,goal_amount,thankyou_title,thankyou_text,thankyou_footer,receipt_from_name,receipt_from_email,cc_receipt,bcc_receipt,receipt_text,is_active,footer_text,amount_block_is_active,honor_block_is_active,honor_block_title,honor_block_text)
VALUES
  ('Help Support CiviCRM!','Do you love CiviCRM? Do you use CiviCRM? Then please support CiviCRM and Contribute NOW by trying out our new online contribution features!',1,1,1,137,'10.00','10000.00','100000.00','Thanks for Your Support!','<p>Thank you for your support. Your contribution will help us build even better tools.</p><p>Please tell your friends and colleagues about CiviCRM!</p>','<p><a href=http://civicrm.org>Back to CiviCRM Home Page</a></p>','CiviCRM Fundraising Dept.','donationFake@civicrm.org','receipt@example.com','bcc@example.com','Your donation is tax deductible under IRS 501(c)(3) regulation. Our tax identification number is: 93-123-4567',1, NULL, 1,NULL, NULL, NULL),
  ('Member Signup and Renewal', 'Members are the life-blood of our organization. If you''re not already a member - please consider signing up today. You can select the membership level the fits your budget and needs below.', 2, 1, NULL, NULL, NULL, NULL, NULL, 'Thanks for Your Support!', 'Thanks for supporting our organization with your membership. You can learn more about membership benefits from our members only page.', NULL, 'Membership Department', 'memberships@civicrm.org', NULL, NULL, 'Thanks for supporting our organization with your membership. You can learn more about membership benefits from our members only page.\r\n\r\nKeep this receipt for your records.', 1, NULL, 0, NULL, NULL,NULL),
('Pledge for CiviCRM!','Do you love CiviCRM? Do you use CiviCRM? Then please support CiviCRM and Pledge NOW by trying out our online contribution features!',1,1,1,NULL,'10.00','10000.00','100000.00','Thanks for Your Support!','<p>Thank you for your support. Your contribution will help us build even better tools like Pledge.</p><p>Please tell your friends and colleagues about CiviPledge!</p>','<p><a href=http://civicrm.org>Back to CiviCRM Home Page</a></p>','CiviCRM Fundraising Dept.','donationFake@civicrm.org','receipt@example.com','bcc@example.com','Your donation is tax deductible under IRS 501(c)(3) regulation. Our tax identification number is: 93-123-4567',1, NULL, 1,NULL, NULL, NULL);
 
INSERT INTO civicrm_contact
    (contact_type, contact_sub_type, legal_identifier, external_identifier, sort_name, display_name, nick_name, home_URL, image_URL, source, preferred_communication_method, preferred_mail_format, do_not_phone, do_not_email, do_not_mail, do_not_trade, hash, is_opt_out,organization_name)
VALUES
    ('Organization',NULL,NULL,NULL,'Inner City Arts','Inner City Arts',NULL,NULL,NULL,NULL,'4','Both',0,0,0,0,'1902067651',0,'Inner City Arts');

INSERT INTO civicrm_membership_type
    (name, description, member_of_contact_id, contribution_type_id, minimum_fee, duration_unit, duration_interval, period_type, fixed_period_start_day, fixed_period_rollover_day, relationship_type_id, relationship_direction, visibility, weight, is_active)
VALUES
    ('General', 'Regular annual membership.', 1, 2, 100.00, 'year', 2, 'rolling', NULL, NULL, 7, 'b_a', 'Public', 1, 1),
    ('Student', 'Discount membership for full-time students.', 1, 1, 50.00, 'year', 1, 'rolling', NULL, NULL, NULL, NULL, 'Public', 2, 1),
    ('Lifetime', 'Lifetime membership.', 1, 2, 1200.00, 'lifetime', 1, 'rolling', NULL, NULL, 7, 'b_a', 'Admin', 3, 1);

INSERT INTO civicrm_membership_block
    (entity_table, entity_id, membership_types, membership_type_default, display_min_fee, is_separate_payment, new_title, new_text, renewal_title, renewal_text, is_required, is_active)
VALUES
    ('civicrm_contribution_page', 2, '1,2', 1, 1, NULL, 'Membership Levels and Fees', 'Please select the appropriate membership level below. You will have a chance to review your selection and the corresponding dues on the next page prior to your credit card being charged.', 'Renew or Upgrade Your Membership', 'Information on your current membership level and expiration date is shown below. You may renew or upgrade at any time - but don''t let your membership lapse!', 1, 1);

INSERT INTO civicrm_pledge_block ( entity_table, entity_id, pledge_frequency_unit, is_pledge_interval, max_reminders, initial_reminder_day, additional_reminder_day)
 VALUES 
    ('civicrm_contribution_page', 3, 'weekmonthyear', 1, 1, 5, 5);
        
INSERT INTO civicrm_premiums 
    VALUES (1, 'civicrm_contribution_page', 1, 1, 'Thank-you Gifts', 'We appreciate your support and invite you to choose from the exciting collection of thank-you gifts below. Minimum contribution amounts for each selection are included in the descriptions. (NOTE: These gifts are shown as examples only. No gifts will be sent to donors.)', 'premiums@example.org', NULL, 1);

INSERT INTO civicrm_product VALUES (1, 'Coffee Mug', 'This heavy-duty mug is great for home or office, coffee or tea or hot chocolate. Show your support to family, friends and colleagues. Choose from three great colors.', 'MUG-101', 'White, Black, Green', NULL, NULL, 12.50, 5.00, 2.25, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO civicrm_premiums_product VALUES (1, 1, 1, 1);

-- sample acl entries
-- Create ACL to edit and view contacts in all groups
INSERT INTO civicrm_acl (name, deny, entity_table, entity_id, operation, object_table, object_id, acl_table, acl_id, is_active) 
VALUES 
('Edit All Contacts', 0, 'civicrm_acl_role', 1, 'Edit', 'civicrm_saved_search', 0, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 2, 'All', 'access CiviMail subscribe/unsubscribe pages', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 2, 'All', 'access all custom data', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 2, 'All', 'make online contributions', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 2, 'All', 'profile listings and forms', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 2, 'All', 'register for events', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 2, 'All', 'view event info', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 2, 'All', 'view event participants', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 0, 'All', 'access CiviMail subscribe/unsubscribe pages', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 0, 'All', 'access all custom data', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 0, 'All', 'make online contributions', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 0, 'All', 'profile listings and forms', NULL, NULL, NULL, 1),
('Core ACL', 0, 'civicrm_acl_role', 0, 'All', 'register for events', NULL, NULL, NULL, 1);

-- Create default Groups for User Permissioning
INSERT INTO civicrm_group
(`id`, `name`, `title`, `description`, `source`, `saved_search_id`, `is_active`, `visibility`, `group_type`, `where_clause`, `select_tables`, `where_tables`)
    VALUES
(1, 'Administrators', 'Administrators', 'Contacts in this group are assigned Administrator role permissions.', NULL, NULL, 1, 'User and User Admin Only', '1', ' ( `civicrm_group_contact-1`.group_id IN (1) AND `civicrm_group_contact-1`.status IN ("Added") ) ', 'a:10:{s:15:"civicrm_contact";i:1;s:18:"civicrm_individual";i:1;s:16:"civicrm_location";i:1;s:15:"civicrm_address";i:1;s:22:"civicrm_state_province";i:1;s:15:"civicrm_country";i:1;s:13:"civicrm_email";i:1;s:13:"civicrm_phone";i:1;s:10:"civicrm_im";i:1;s:25:"`civicrm_group_contact-1`";s:114:" LEFT JOIN civicrm_group_contact `civicrm_group_contact-1` ON contact_a.id = `civicrm_group_contact-1`.contact_id ";}', 'a:2:{s:15:"civicrm_contact";i:1;s:25:"`civicrm_group_contact-1`";s:114:" LEFT JOIN civicrm_group_contact `civicrm_group_contact-1` ON contact_a.id = `civicrm_group_contact-1`.contact_id ";}');

-- Assign above Group (entity) to the Administrator Role
INSERT INTO civicrm_acl_entity_role
    (`acl_role_id`, `entity_table`, `entity_id`, `is_active`)
VALUES
    (1, 'civicrm_group', 1, 1);

-- Add sample activity type

SELECT @option_group_id_act            := max(id) from civicrm_option_group where name = 'activity_type';

INSERT INTO 
   `civicrm_option_value` (`option_group_id`, `label`, `value`, `name`, `grouping`, `filter`, `is_default`, `weight`, `description`, `is_optgroup`, `is_reserved`, `is_active`) 
VALUES
   (@option_group_id_act, 'Interview', 12, 'Interview',  NULL, 0, NULL, 12, 'Conduct a phone or in person interview.', 0, 0, 1);