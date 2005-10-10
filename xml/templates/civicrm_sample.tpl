
-- Sample Extended Property Group and Fields
INSERT INTO civicrm_custom_group
    (domain_id, name, title, extends, style, collapse_display, help_pre, weight, is_active)
VALUES
    (%%CIVICRM_DOMAIN_ID%%, 'voter_info', 'Voter Info', 'Individual', 'Inline', 0, 'Please complete the voter information fields as data becomes available for this contact.', 1, 1);

INSERT INTO civicrm_custom_field
    (custom_group_id, name, label, data_type, html_type, is_required, weight, help_post, is_active, is_searchable, options_per_line)
VALUES
    (1, 'registered_voter', 'Registered Voter?', 'Boolean', 'Radio', 0, 1, '', 1, 1, NULL),
    (1, 'party_registration', 'Party Registration', 'String', 'Text', 0, 2, 'If contact is registered, enter party name here.', 1, 1, NULL),   
    (1, 'date_last_voted', 'Date Last Voted', 'Date', 'Select Date', 0, 3, '', 1, 1, NULL),
    (1, 'voting_precinct', 'Voting Precinct', 'Int', 'Text', 0, 4, 'Precinct number - if available.', 1, 1, NULL),
    (1, 'most_important_issue', 'Most Important Issue', 'String', 'Radio', 0, 5, '', 1, 1, NULL),
    (1, 'gotv_experience', 'GOTV Experience', 'String', 'Checkbox', 0, 6, 'Which Get Out the Vote activities have you done in the past.', 1, 1, 1),
    (1, 'marital_status', 'Marital Status', 'String', 'Select', 0, 7, '', 1, 1, NULL);

INSERT INTO civicrm_custom_option
    (custom_field_id,label,value,weight,is_active)
VALUES
    (5, 'Education', 'Edu', 1, 1),
    (5, 'Environment', 'Env', 2, 1),
    (5, 'Social Justice', 'SocJus', 3, 1),
    (6, 'Host House Meetings', 'HM', 1, 1),
    (6, 'Phone Banking', 'PB', 2, 1),
    (6, 'Precinct Walking', 'PW', 3, 1),
    (6, 'Speakers Bureau', 'SB', 4, 1),
    (7, 'Single', 'S', 1, 1),
    (7, 'Married', 'M', 2, 1),
    (7, 'Domestic Partner', 'D', 3, 1),
    (7, 'Widowed', 'W', 4, 1),
    (7, 'Other', 'O', 5, 1);

-- Custom data for educational qualifications
INSERT INTO civicrm_custom_group
    (domain_id, name, title, extends, style, collapse_display, help_pre, weight, is_active)
VALUES
    (%%CIVICRM_DOMAIN_ID%%, 'education', 'Education Qualification', 'Individual', 'Tab', 0, 'Please furnish educational history starting from high school', 2, 1);

INSERT INTO civicrm_custom_field
    (custom_group_id, name, label, data_type, html_type, is_required, weight, help_post, is_active)
VALUES
    (2, 'degree', 'Degree Obtained', 'String', 'Text', 1, 2, '', 1),
    (2, 'school_college', 'School / College', 'String', 'Text', 0, 1, '', 1),
    (2, 'marks', 'Marks Obtained', 'String', 'Text', 0, 3, '', 1),
    (2, 'date_of_degree', 'Degree date', 'Date', 'Select Date', 0, 4, '', 1);

INSERT INTO civicrm_uf_group
    (domain_id, is_active, form_type, title)
VALUES
    (%%CIVICRM_DOMAIN_ID%%, 1, 'CiviCRM Profile', 'Constituent Information');

INSERT INTO civicrm_uf_field
    (uf_group_id, field_name, weight, is_active, is_view, is_required, is_registration, is_match, visibility, help_post, in_selector)
VALUES
    (1,'first_name',1,1,0,1,1,1,'Public User Pages and Listings','',1),
    (1,'last_name',2,1,0,1,1,1,'Public User Pages and Listings','First and last name will be shared with other visitors to the site.',1),
    (1,'street_address',3,1,0,0,1,0,'User and User Admin Only','',0),
    (1,'city',4,1,0,0,1,0,'User and User Admin Only','',0),
    (1,'postal_code',5,1,0,0,1,0,'User and User Admin Only','',0),
    (1,'state_province',6,1,0,0,1,0,'Public User Pages and Listings','Your state/province and country of residence will be shared with others so folks can find others in their community.',1),
    (1,'country',7,1,0,0,0,0,'Public User Pages and Listings','',0),
    (1,'email',8,1,1,0,0,1,'Public User Pages','',1),
    (1,'custom_5',9,1,0,0,1,0,'Public User Pages and Listings','',1),
    (1,'custom_6',10,1,0,0,1,0,'Public User Pages and Listings','',1),
    (1,'custom_7',11,1,0,0,1,0,'Public User Pages and Listings','',0);

