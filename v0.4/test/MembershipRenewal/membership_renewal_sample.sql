-- Createing required membership types
INSERT INTO `civicrm_membership_type` 
	(`domain_id`, `name`, `description`, `member_of_contact_id`, `contribution_type_id`, `minimum_fee`, `duration_unit`, `duration_interval`, `period_type`, `fixed_period_start_day`, `fixed_period_rollover_day`, `relationship_type_id`, `relationship_direction`, `visibility`, `weight`, `renewal_msg_id`, `renewal_reminder_day`, `is_active`) 
VALUES 
	(1, 'S1', 'Membership Type Created for testing Scenario 1', 1, 1, 50.00, 'year', 1, 'rolling', NULL, NULL, NULL, NULL, 'Admin', 4, NULL, NULL, 1),
	(1, 'S2', 'Membership Type Created for testing Scenario 2', 1, 1, 60.00, 'year', 1, 'fixed', 0701, 0501, NULL, NULL, 'Admin', 5, NULL, NULL, 1),
	(1, 'S3', 'Membership Type Created for testing Scenario 3', 1, 1, 50.00, 'month', 1, 'rolling', NULL, NULL, NULL, NULL, 'Admin', 4, NULL, NULL, 1),
	(1, 'S4', 'Membership Type Created for testing Scenario 4', 1, 1, 50.00, 'month', 3, 'rolling', NULL, NULL, NULL, NULL, 'Admin', 7, NULL, NULL, 1),
	(1, 'S5', 'Membership Type Created for testing Scenario 5', 1, 1, 60.00, 'month', 1, 'fixed', 0520, 0612, NULL, NULL, 'Admin', 9, NULL, NULL, 1),
	(1, 'S6', 'Membership Type Created for testing Scenario 6', 1, 1, 60.00, 'month', 3, 'fixed', 0901, 1101, NULL, NULL, 'Admin', 9, NULL, NULL, 1),
	(1, 'S7', 'Membership Type Created for testing Scenario 7', 1, 1, 50.00, 'day', 30, 'rolling', NULL, NULL, NULL, NULL, 'Admin', 10, NULL, NULL, 1),
	(1, 'S8', 'Membership Type Created for testing Scenario 8', 1, 1, 60.00, 'day', 30, 'fixed', 1210, 1220, NULL, NULL, 'Admin', 11, NULL, NULL, 1),
	(1, 'S9', 'Membership Type Created for testing Scenario 9', 1, 1, 50.00, 'year', 1, 'rolling', NULL, NULL, NULL, NULL, 'Admin', 12, NULL, NULL, 1),
	(1, 'S10', 'Membership Type Created for testing Scenario 10', 1, 1, 60.00, 'year', 1, 'fixed', 0101, 1101, NULL, NULL, 'Admin', 13, NULL, NULL, 1),
	(1, 'S11', 'Membership Type Created for testing Scenario 11', 1, 1, 50.00, 'month', 1, 'rolling', NULL, NULL, NULL, NULL, 'Admin', 14, NULL, NULL, 1),
	(1, 'S12', 'Membership Type Created for testing Scenario 12', 1, 1, 60.00, 'month', 1, 'fixed', 0501,0520, NULL, NULL, 'Admin', 15, NULL, NULL, 1),
	(1, 'S13', 'Membership Type Created for testing Scenario 13', 1, 1, 50.00, 'day', 30, 'rolling', NULL, NULL, NULL, NULL, 'Admin', 16, NULL, NULL, 1),
	(1, 'S14', 'Membership Type Created for testing Scenario 14', 1, 1, 60.00, 'day', 30, 'fixed', 0501, 0518, NULL, NULL, 'Admin', 17, NULL, NULL, 1),
	(1, 'S15', 'Membership Type Created for testing Scenario 15', 1, 1, 60.00, 'year', 1, 'fixed', 0101, 1101, NULL, NULL, 'Admin', 18, NULL, NULL, 1),
	(1, 'S16', 'Membership Type Created for testing Scenario 16', 1, 1, 60.00, 'month', 1, 'fixed', 0520,0615, NULL, NULL, 'Admin', 19, NULL, NULL, 1),
	(1, 'S17', 'Membership Type Created for testing Scenario 17', 1, 1, 50.00, 'year', 1, 'rolling', NULL, NULL, NULL, NULL, 'Admin', 20, NULL, NULL, 1),
	(1, 'S18', 'Membership Type Created for testing Scenario 18', 1, 1, 60.00, 'year', 1, 'fixed', 0101, 1101, NULL, NULL, 'Admin', 21, NULL, NULL, 1);

-- Selecting required membership types
SELECT @membership_type_id_S1 := max(id) from civicrm_membership_type where name = 'S1';
SELECT @membership_type_id_S2 := max(id) from civicrm_membership_type where name = 'S2';
SELECT @membership_type_id_S3 := max(id) from civicrm_membership_type where name = 'S3';
SELECT @membership_type_id_S4 := max(id) from civicrm_membership_type where name = 'S4';
SELECT @membership_type_id_S5 := max(id) from civicrm_membership_type where name = 'S5';
SELECT @membership_type_id_S6 := max(id) from civicrm_membership_type where name = 'S6';
SELECT @membership_type_id_S7 := max(id) from civicrm_membership_type where name = 'S7';
SELECT @membership_type_id_S8 := max(id) from civicrm_membership_type where name = 'S8';
SELECT @membership_type_id_S9 := max(id) from civicrm_membership_type where name = 'S9';
SELECT @membership_type_id_S10 := max(id) from civicrm_membership_type where name = 'S10';
SELECT @membership_type_id_S11 := max(id) from civicrm_membership_type where name = 'S11';
SELECT @membership_type_id_S12 := max(id) from civicrm_membership_type where name = 'S12';
SELECT @membership_type_id_S13 := max(id) from civicrm_membership_type where name = 'S13';
SELECT @membership_type_id_S14 := max(id) from civicrm_membership_type where name = 'S14';
SELECT @membership_type_id_S15 := max(id) from civicrm_membership_type where name = 'S15';
SELECT @membership_type_id_S16 := max(id) from civicrm_membership_type where name = 'S16';
SELECT @membership_type_id_S17 := max(id) from civicrm_membership_type where name = 'S17';
SELECT @membership_type_id_S18 := max(id) from civicrm_membership_type where name = 'S18';

-- Populating membership data
-- S1
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(55, @membership_type_id_S1, '2007-01-01', '2007-01-01', '2007-12-31', 'Payment', 2, NULL, NULL, NULL, 0),
(64, @membership_type_id_S1, '2007-03-01', '2007-03-01', '2008-02-29', 'Check', 2, NULL, NULL, NULL, 0),
(82, @membership_type_id_S1, '2007-10-01', '2007-10-01', '2008-09-30', 'Payment', 2, NULL, NULL, NULL, 0);
-- S2
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(55, @membership_type_id_S2, '2007-05-12', '2006-07-01', '2008-06-30', 'Payment', 2, NULL, NULL, NULL, 0),
(64, @membership_type_id_S2, '2007-05-03', '2006-07-01', '2008-06-30', 'Check', 2, NULL, NULL, NULL, 0),
(82, @membership_type_id_S2, '2007-05-21', '2006-07-01', '2008-06-30', 'Payment', 2, NULL, NULL, NULL, 0);
-- S3
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(64, @membership_type_id_S3, '2007-12-01', '2007-12-01', '2008-12-31', 'Payment', 2, NULL, NULL, NULL, 0);
-- S4
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(64, @membership_type_id_S4, '2007-11-01', '2007-11-01', '2008-01-31', 'Payment', 2, NULL, NULL, NULL, 0);
-- S5
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(55, @membership_type_id_S5, '2007-06-13', '2007-05-20', '2007-07-19', 'Payment', 2, NULL, NULL, NULL, 0),
(64, @membership_type_id_S5, '2007-06-15', '2007-05-20', '2007-07-19', 'Check', 2, NULL, NULL, NULL, 0),
(82, @membership_type_id_S5, '2007-06-18', '2007-05-20', '2007-07-19', 'Payment', 2, NULL, NULL, NULL, 0);
-- S6
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(55, @membership_type_id_S6, '2007-11-03', '2007-09-01', '2008-02-29', 'Payment', 2, NULL, NULL, NULL, 0),
(64, @membership_type_id_S6, '2007-11-12', '2007-09-01', '2008-02-29', 'Check', 2, NULL, NULL, NULL, 0),
(82, @membership_type_id_S6, '2007-11-21', '2007-09-01', '2008-02-29', 'Payment', 2, NULL, NULL, NULL, 0);
-- S7
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(64, @membership_type_id_S7, '2007-12-06', '2007-12-06', '2008-01-05', 'Payment', 2, NULL, NULL, NULL, 0);
-- S8
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(64, @membership_type_id_S8, '2007-12-12', '2007-12-10', '2008-01-09', 'Payment', 2, NULL, NULL, NULL, 0);
-- S9
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(55, @membership_type_id_S9, '2006-01-01', '2006-01-01', '2006-12-31', 'Payment', 4, NULL, NULL, NULL, 0),
(64, @membership_type_id_S9, '2006-03-01', '2006-03-01', '2007-02-28', 'Check', 4, NULL, NULL, NULL, 0),
(82, @membership_type_id_S9, '2005-10-01', '2005-10-01', '2006-09-30', 'Payment', 4, NULL, NULL, NULL, 0);
-- S10
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(55, @membership_type_id_S10, '2004-11-02', '2004-01-01', '2005-12-31', 'Payment', 4, NULL, NULL, NULL, 0),
(64, @membership_type_id_S10, '2004-11-12', '2004-01-01', '2005-12-31', 'Check', 4, NULL, NULL, NULL, 0),
(82, @membership_type_id_S10, '2004-11-27', '2004-01-01', '2005-12-31', 'Payment', 4, NULL, NULL, NULL, 0);
-- S11
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(55, @membership_type_id_S11, '2006-09-01', '2006-09-01', '2006-09-30', 'Payment', 4, NULL, NULL, NULL, 0),
(64, @membership_type_id_S11, '2006-10-01', '2006-10-01', '2006-10-31', 'Check', 4, NULL, NULL, NULL, 0),
(82, @membership_type_id_S11, '2006-12-15', '2006-12-15', '2007-01-14', 'Payment', 4, NULL, NULL, NULL, 0);
-- S12
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(55, @membership_type_id_S12, '2006-05-21', '2006-05-01', '2006-06-30', 'Payment', 4, NULL, NULL, NULL, 0),
(64, @membership_type_id_S12, '2006-05-22', '2006-05-01', '2006-06-30', 'Check', 4, NULL, NULL, NULL, 0),
(82, @membership_type_id_S12, '2006-05-23', '2006-05-01', '2006-06-30', 'Payment', 4, NULL, NULL, NULL, 0);
-- S13
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(55, @membership_type_id_S13, '2005-05-01', '2005-05-01', '2005-05-30', 'Payment', 4, NULL, NULL, NULL, 0),
(64, @membership_type_id_S13, '2005-05-03', '2005-05-03', '2005-06-01', 'Check', 4, NULL, NULL, NULL, 0),
(82, @membership_type_id_S13, '2005-05-09', '2005-05-09', '2005-06-07', 'Payment', 4, NULL, NULL, NULL, 0);
-- S14
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(55, @membership_type_id_S14, '2006-05-19', '2006-05-01', '2005-06-29', 'Payment', 4, NULL, NULL, NULL, 0),
(64, @membership_type_id_S14, '2006-05-20', '2006-05-01', '2005-06-29', 'Check', 4, NULL, NULL, NULL, 0),
(82, @membership_type_id_S14, '2006-05-21', '2006-05-01', '2005-06-29', 'Payment', 4, NULL, NULL, NULL, 0);
-- S15
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(55, @membership_type_id_S15, '2004-10-03', '2004-01-01', '2004-12-31', 'Payment', 4, NULL, NULL, NULL, 0),
(64, @membership_type_id_S15, '2004-10-12', '2004-01-01', '2004-12-31', 'Check', 4, NULL, NULL, NULL, 0),
(82, @membership_type_id_S15, '2004-10-30', '2004-01-01', '2004-12-31', 'Payment', 4, NULL, NULL, NULL, 0);
-- S16
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(55, @membership_type_id_S16, '2005-06-03', '2005-05-20', '2005-04-19', 'Payment', 4, NULL, NULL, NULL, 0),
(64, @membership_type_id_S16, '2005-06-09', '2005-05-20', '2005-04-19', 'Check', 4, NULL, NULL, NULL, 0),
(82, @membership_type_id_S16, '2005-06-14', '2005-05-20', '2005-04-19', 'Payment', 4, NULL, NULL, NULL, 0);
-- S17
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(64, @membership_type_id_S17, '2006-05-01', '2006-05-01', '2007-04-30', 'Check', 2, NULL, NULL, NULL, 0);
-- S18
INSERT INTO `civicrm_membership` 
(`contact_id`, `membership_type_id`, `join_date`, `start_date`, `end_date`, `source`, `status_id`, `is_override`, `reminder_date`, `owner_membership_id`, `is_test`) 
VALUES 
(64, @membership_type_id_S18, '2006-09-10', '2006-01-01', '2006-12-31', 'Check', 2, NULL, NULL, NULL, 0);
