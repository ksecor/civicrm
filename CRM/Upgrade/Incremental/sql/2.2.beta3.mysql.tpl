-- CRM-4167

ALTER TABLE `civicrm_event`
  ADD `allow_same_participant_emails` tinyint(4) default '0' COMMENT 'if true - allows the user to register multiple registrations from same email address.';

-- CRM-4166
INSERT INTO  `civicrm_payment_processor_type` 
(name, title, description, is_active, is_default, user_name_label, password_label, signature_label, subject_label, class_name, url_site_default, url_api_default, url_recur_default, url_button_default, url_site_test_default, url_api_test_default, url_recur_test_default, url_button_test_default, billing_mode, is_recur )
VALUES
('Elavon','{ts escape="sql"}Elavon Payment Processor{/ts}','{ts escape="sql"}Elavon / Nova Virtual Merchant{/ts}',1,0,'{ts escape="sql"}SSL Merchant ID {/ts}','{ts escape="sql"}SSL User ID{/ts}','{ts escape="sql"}SSL PIN{/ts}',NULL,'Payment_Elavon','https://www.myvirtualmerchant.com/VirtualMerchant/processxml.do',NULL,NULL,NULL,'https://www.myvirtualmerchant.com/VirtualMerchant/processxml.do',NULL,NULL,NULL,1,0)
ON DUPLICATE KEY UPDATE civicrm_payment_processor_type.name=name;
