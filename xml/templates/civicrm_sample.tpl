
-- Sample Extended Property Group and Fields
INSERT INTO civicrm_custom_group
    (domain_id, name, title, extends, style, collapse_display, help_pre, weight, is_active)
VALUES
    (%%CIVICRM_DOMAIN_ID%%, 'voter_info', 'Voter Info', 'Individual', 'Tab', 0, 'Please complete the voter information fields as data becomes available for this contact.', 1, 1);

INSERT INTO civicrm_custom_field
    (custom_group_id, name, label, data_type, html_type, is_required, weight, help_post, is_active)
VALUES
    (1, 'registered_voter', 'Registered Voter?', 'Boolean', 'Radio', 1, 1, '', 1);
    
INSERT INTO civicrm_custom_field
    (custom_group_id, name, label, data_type, html_type, is_required, weight, help_post, is_active, is_searchable)
VALUES
    (1, 'party_registration', 'Party Registration', 'String', 'Text', 0, 2, 'If contact is registered, enter party name here.', 1, 1);
    
INSERT INTO civicrm_custom_field
    (custom_group_id, name, label, data_type, html_type, is_required, weight, help_post, is_active)
VALUES
    (1, 'date_last_voted', 'Date Last Voted', 'Date', 'Select Date', 0, 3, '', 1);

INSERT INTO civicrm_custom_field
    (custom_group_id, name, label, data_type, html_type, is_required, weight, help_post, is_active, is_searchable)
VALUES
    (1, 'voting_precinct', 'Voting Precinct', 'Int', 'Text', 0, 4, 'Precinct number - if available.', 1, 1);


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
    (%%CIVICRM_DOMAIN_ID%%, 1, 'CiviCRM Profile', 'CiviCRM Name and Address');

INSERT INTO civicrm_uf_field
    (uf_group_id, field_name, weight, is_active, is_view, is_required, is_registration, is_match, visibility, listings_title, help_post)
VALUES
    (1,'first_name',1,1,0,1,1,1,'Public User Pages','',''),
    (1,'last_name',2,1,0,1,1,1,'Public User Pages','','First and last name will be shared with other visitors to the site.'),
    (1,'street_address',3,1,0,0,0,0,'User and User Admin Only','','Your street address will not be shared with visitors to the site.'),
    (1,'city',4,1,0,0,0,0,'Public User Pages','','Your postal code and city of residence will be shared with others so folks can find others in their community.'),
    (1,'postal_code',5,1,0,0,0,0,'Public User Pages','',''),
    (1,'state_province',6,1,0,0,0,0,'Public User Pages','','Your state/province and country of residence will be shared with others so folks can find others in their community.'),
    (1,'country',7,1,0,0,0,0,'Public User Pages','',''),
    (1,'email',8,1,1,0,0,1,'Public User Pages','','');

