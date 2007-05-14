-- Createing required membership types
INSERT INTO `civicrm_membership_type` 
	(`domain_id`, `name`, `description`, `member_of_contact_id`, `contribution_type_id`, `minimum_fee`, `duration_unit`, `duration_interval`, `period_type`, `fixed_period_start_day`, `fixed_period_rollover_day`, `relationship_type_id`, `relationship_direction`, `visibility`, `weight`, `renewal_msg_id`, `renewal_reminder_day`, `is_active`) 
VALUES 
	(1, 'S1', 'Membership Type Created for testing Scenario 1', 1, 1, 50.00, 'year', 1, 'rolling', NULL, NULL, NULL, NULL, 'Admin', 4, NULL, NULL, 1),
	(1, 'S2', 'Membership Type Created for testing Scenario 2', 1, 1, 60.00, 'year', 1, 'fixed', 0101, 0431, NULL, NULL, 'Admin', 5, NULL, NULL, 1);

-- Selecting required membership types
SELECT @membership_type_id_S1 := max(id) from civicrm_membership_type where name = 'S1';
SELECT @membership_type_id_S2 := max(id) from civicrm_membership_type where name = 'S2';

-- Populating membership data
-- S1
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(55, @membership_type_id_S1, '2007-01-01', '2007-01-01', '2007-12-31', 'Payment', 2, NULL, NULL, NULL, 0),
(43, @membership_type_id_S1, '2007-02-01', '2007-02-01', '2008-01-31', 'Donation', 2, NULL, NULL, NULL, 0),
(64, @membership_type_id_S1, '2007-03-01', '2007-03-01', '2008-02-29', 'Check', 2, NULL, NULL, NULL, 0),
(41, @membership_type_id_S1, '2007-06-01', '2007-06-01', '2008-05-31', 'Check', 2, NULL, NULL, NULL, 0),
(82, @membership_type_id_S1, '2007-10-01', '2007-10-01', '2008-09-30', 'Payment', 2, NULL, NULL, NULL, 0);
-- S2
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(55, @membership_type_id_S2, '2007-04-01', '2007-01-01', '2008-12-31', 'Payment', 2, NULL, NULL, NULL, 0),
(43, @membership_type_id_S2, '2007-04-02', '2007-01-01', '2008-12-31', 'Donation', 2, NULL, NULL, NULL, 0),
(64, @membership_type_id_S2, '2007-04-03', '2007-01-01', '2008-12-31', 'Check', 2, NULL, NULL, NULL, 0),
(41, @membership_type_id_S2, '2007-04-06', '2007-01-01', '2008-12-31', 'Check', 2, NULL, NULL, NULL, 0),
(82, @membership_type_id_S2, '2007-04-10', '2007-01-01', '2008-12-31', 'Payment', 2, NULL, NULL, NULL, 0);
