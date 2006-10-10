
-- Sample Extended Property Group and Fields
INSERT INTO civicrm_custom_group
    (domain_id, name, title, extends, style, collapse_display, help_pre, weight, is_active)
VALUES
    (%%CIVICRM_DOMAIN_ID%%, 'voter_info', 'Voter Info', 'Individual', 'Inline', 0, 'Please complete the voter information fields as data becomes available for this contact.', 1, 1),
    (%%CIVICRM_DOMAIN_ID%%, 'education', 'Education Qualification', 'Individual', 'Tab', 0, 'Please furnish educational history starting from high school', 2, 1);

INSERT INTO civicrm_custom_field
    (custom_group_id, name, label, data_type, html_type, is_required, weight, help_post, is_active, is_searchable, options_per_line)
VALUES
    (1, 'registered_voter', 'Registered Voter?', 'Boolean', 'Radio', 0, 1, '', 1, 1, NULL),
    (1, 'party_registration', 'Party Registration', 'String', 'Text', 0, 2, 'If contact is registered, enter party name here.', 1, 1, NULL),   
    (1, 'date_last_voted', 'Date Last Voted', 'Date', 'Select Date', 0, 3, '', 1, 1, NULL),
    (1, 'voting_precinct', 'Voting Precinct', 'Int', 'Text', 0, 4, 'Precinct number - if available.', 1, 1, NULL),
    (1, 'most_important_issue', 'Most Important Issue', 'String', 'Radio', 0, 5, '', 1, 1, NULL),
    (1, 'gotv_experience', 'GOTV Experience', 'String', 'Checkbox', 0, 6, 'Which Get Out the Vote activities have you done in the past.', 1, 1, 1),
    (1, 'marital_status', 'Marital Status', 'String', 'Select', 0, 7, '', 1, 1, NULL),
    (2, 'degree', 'Degree Obtained', 'String', 'Text', 1, 2, '', 1, 0, NULL),
    (2, 'school_college', 'School / College', 'String', 'Text', 0, 1, '', 1, 0, NULL),
    (2, 'marks', 'Marks Obtained', 'String', 'Text', 0, 3, '', 1, 0, NULL),
    (2, 'date_of_degree', 'Degree date', 'Date', 'Select Date', 0, 4, '', 1, 0, NULL);

INSERT INTO civicrm_custom_option
    (entity_table,entity_id,label,value,weight,is_active)
VALUES
    ('civicrm_custom_field', 5, 'Education', 'Edu', 1, 1),
    ('civicrm_custom_field', 5, 'Environment', 'Env', 2, 1),
    ('civicrm_custom_field', 5, 'Social Justice', 'SocJus', 3, 1),
    ('civicrm_custom_field', 6, 'Host House Meetings', 'HM', 1, 1),
    ('civicrm_custom_field', 6, 'Phone Banking', 'PB', 2, 1),
    ('civicrm_custom_field', 6, 'Precinct Walking', 'PW', 3, 1),
    ('civicrm_custom_field', 6, 'Speakers Bureau', 'SB', 4, 1),
    ('civicrm_custom_field', 7, 'Single', 'S', 1, 1),
    ('civicrm_custom_field', 7, 'Married', 'M', 2, 1),
    ('civicrm_custom_field', 7, 'Domestic Partner', 'D', 3, 1),
    ('civicrm_custom_field', 7, 'Widowed', 'W', 4, 1),
    ('civicrm_custom_field', 7, 'Other', 'O', 5, 1),
    ('civicrm_contribution_page',1,'Friend','0.10',1,1),
    ('civicrm_contribution_page',1,'Supporter','0.50',2,1),
    ('civicrm_contribution_page',1,'Booster','1.00',3,1),
    ('civicrm_contribution_page',1,'Sustainer','5.00',4,1);

INSERT INTO civicrm_uf_group
    (domain_id, is_active, form_type, title, help_pre)
VALUES
    (%%CIVICRM_DOMAIN_ID%%, 1, 'CiviCRM Profile', 'Constituent Information', ''),
    (%%CIVICRM_DOMAIN_ID%%, 1, 'CiviCRM Profile', 'Contributor Info', 'Tell us about the issue that is most important for you, and your experience in prior campaigns.' );

INSERT INTO civicrm_uf_join
   (is_active,module,entity_table,entity_id,weight,uf_group_id)
VALUES
   (1,'User Registration','','',1,1),
   (1,'User Account','','',1,1),
   (1,'Profile','','',1,1), 
   (1,'CiviContribute','civicrm_contribution_page',1,2,2);

INSERT INTO civicrm_contribution_page
  (domain_id,title,intro_text,contribution_type_id,is_allow_other_amount,default_amount,min_amount,max_amount,goal_amount,thankyou_title,thankyou_text,thankyou_footer,receipt_from_name,receipt_from_email,is_email_receipt,cc_receipt,bcc_receipt,receipt_text,is_active,footer_text,amount_block_is_active,is_thermometer,thermometer_title,honor_block_is_active,honor_block_title,honor_block_text)
VALUES
  (%%CIVICRM_DOMAIN_ID%%,'Help Support CiviCRM!','Do you love CiviCRM? Do you use CiviCRM? Then please support CiviCRM and Contribute NOW by trying out our new online contribution features!',1,1,'500.00','10.00','10000.00','100000.00','Thanks for Your Support!','<p>Thank you for your support. Your contribution will help us build even better tools.</p><p>Please tell your friends and colleagues about CiviCRM!</p>','<p><a href=http://www.civicrm.org>Back to CiviCRM Home Page</a></p>','CiviCRM Fundraising Dept.','donations@civicrm.org',1,'receipt@example.com','bcc@example.com','Your donation is tax deductible under IRS 501(c)(3) regulation. Our tax identification number is: 93-123-4567',1, NULL, 1, 1,'Track Our Progress',NULL, NULL, NULL),
  (%%CIVICRM_DOMAIN_ID%%, 'Member Signup and Renewal', 'Members are the life-blood of our organization. If you''re not already a member - please consider signing up today. You can select the membership level the fits your budget and needs below.', 2, NULL, NULL, NULL, NULL, NULL, 'Thanks for Your Support!', 'Thanks for supporting our organization with your membership. You can learn more about membership benefits from our members only page.', NULL, 1, 'Membership Department', 'memberships@civicrm.org', NULL, NULL, 'Thanks for supporting our organization with your membership. You can learn more about membership benefits from our members only page.\r\n\r\nKeep this receipt for your records.', 1, NULL, NULL, NULL, NULL,NULL, NULL, NULL);

INSERT INTO civicrm_contact
    (domain_id, contact_type, contact_sub_type, legal_identifier, external_identifier, sort_name, display_name, nick_name, home_URL, image_URL, source, preferred_communication_method, preferred_mail_format, do_not_phone, do_not_email, do_not_mail, do_not_trade, hash, is_opt_out)
VALUES
    (%%CIVICRM_DOMAIN_ID%%,'Organization',NULL,NULL,NULL,'Inner City Arts','Inner City Arts',NULL,NULL,NULL,NULL,'4','Both',0,0,0,0,'1902067651',0);

INSERT INTO civicrm_organization
    (contact_id, organization_name, legal_name, sic_code, primary_contact_id)
VALUES
    (1,'Inner City Arts',NULL,NULL,NULL);

INSERT INTO civicrm_membership_type
    (domain_id, name, description, member_of_contact_id, contribution_type_id, minimum_fee, duration_unit, duration_interval, period_type, fixed_period_start_day, fixed_period_rollover_day, relationship_type_id, visibility, weight, is_active)
VALUES
    (%%CIVICRM_DOMAIN_ID%%, 'General', 'Regular annual membership.', 1, 2, 100.00, 'year', 2, 'rolling', NULL, NULL, 7, 'Public', 1, 1),
    (%%CIVICRM_DOMAIN_ID%%, 'Student', 'Discount membership for full-time students.', 1, 1, 50.00, 'year', 1, 'rolling', NULL, NULL, 7, 'Public', 2, 1),
    (%%CIVICRM_DOMAIN_ID%%, 'Lifetime', 'Lifetime membership.', 1, 2, 1200.00, 'lifetime', 1, 'rolling', NULL, NULL, 7, 'Admin', 3, 1);

INSERT INTO civicrm_membership_block
    (entity_table, entity_id, membership_types, membership_type_default, display_min_fee, is_separate_payment, new_title, new_text, renewal_title, renewal_text, is_required, is_active)
VALUES
    ('civicrm_contribution_page', 2, '1,2', 1, 1, NULL, 'Membership Levels and Fees', 'Please select the appropriate membership level below. You will have a chance to review your selection and the corresponding dues on the next page prior to your credit card being charged.', 'Renew or Upgrade Your Membership', 'Information on your current membership level and expiration date is shown below. You may renew or upgrade at any time - but don''t let your membership lapse!', 1, 1);

INSERT INTO civicrm_uf_field
    (uf_group_id, field_name, weight, is_active, is_view, is_required, visibility, help_post, in_selector, location_type_id,is_searchable,field_type, label)
VALUES
    (1,'first_name',1,1,0,1,'Public User Pages and Listings','',0,NULL,1,'Individual', 'First Name'),
    (1,'last_name',2,1,0,1,'Public User Pages and Listings','First and last name will be shared with other visitors to the site.',0,NULL,1,'Individual', 'Last Name'),
    (1,'street_address',3,1,0,0,'User and User Admin Only','',0,1,1,'Individual', 'Street Address (Home)'),
    (1,'city',4,1,0,0,'User and User Admin Only','',0,1,1,'Individual', 'City (Home)'),
    (1,'postal_code',5,1,0,0,'User and User Admin Only','',0,1,1,'Individual', 'Postal Code (Home)'),
    (1,'state_province',6,1,0,0,'Public User Pages and Listings','Your state/province and country of residence will be shared with others so folks can find others in their community.',1,1,1,'Individual', 'State (Home)'),
    (1,'country',7,1,0,0,'Public User Pages and Listings','',0,1,1,'Individual', 'Country (Home)'),
    (1,'email',8,1,0,0,'Public User Pages and Listings','',1,1,1,'Individual', 'Email (Home)'),
    (1,'custom_5',9,1,0,0,'Public User Pages and Listings','',1,NULL,1,'Individual', 'Most Important Issue'),
    (1,'custom_6',10,1,0,0,'Public User Pages and Listings','',1,NULL,1,'Individual', 'GOTV Experience'),
    (1,'custom_7',11,1,0,0,'Public User Pages and Listings','',0,NULL,1,'Individual', 'Marital Status'),
    (2,'custom_5',1,1,0,0,'User and User Admin Only','',0,NULL,1,'Individual', 'Most Important Issue'),
    (2,'custom_6',2,1,0,0,'User and User Admin Only','',0,NULL,1,'Individual', 'GOTV Experience');
    
INSERT INTO civicrm_premiums 
    VALUES (%%CIVICRM_DOMAIN_ID%%, 'civicrm_contribution_page', 1, 1, 'Thank-you Gifts', 'We appreciate your support and invite you to choose from the exciting collection of thank-you gifts below. Minimum contribution amounts for each selection are included in the descriptions. ', 'premiums@example.org', NULL, 1);

INSERT INTO civicrm_product VALUES (1, %%CIVICRM_DOMAIN_ID%%,'Coffee Mug', 'This heavy-duty mug is great for home or office, coffee or tea or hot chocolate. Show your support to family, friends and colleagues. Choose from three great colors.', 'MUG-101', 'White, Black, Green', NULL, NULL, 12.50, 5.00, 2.25, 1, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO civicrm_premiums_product VALUES (1, 1, 1, 1);


