INSERT INTO civicrm_option_group
  (name,                            description,                                                         is_reserved, is_active) VALUES
  ('msg_tpl_workflow_contribution', '{ts escape="sql"}Message Template Workflow for Contributions{/ts}', 0,           1),
  ('msg_tpl_workflow_event',        '{ts escape="sql"}Message Template Workflow for Events{/ts}',        0,           1);

SELECT @tpl_ogid_contribution := MAX(id) FROM civicrm_option_group WHERE name = 'msg_tpl_workflow_contribution';
SELECT @tpl_ogid_event        := MAX(id) FROM civicrm_option_group WHERE name = 'msg_tpl_workflow_event';

{capture assign=contribution_dupalert_subj}{fetch file="../xml/templates/message_templates/contribution_dupalert_subj.tpl"}{/capture}
{capture assign=contribution_dupalert_text}{fetch file="../xml/templates/message_templates/contribution_dupalert_text.tpl"}{/capture}
{capture assign=contribution_dupalert_html}{fetch file="../xml/templates/message_templates/contribution_dupalert_html.tpl"}{/capture} {* FIXME: make it an actual HTML template *}
{capture assign=contribution_receipt_subj}{fetch file="../xml/templates/message_templates/contribution_receipt_subj.tpl"}{/capture}
{capture assign=contribution_receipt_text}{fetch file="../xml/templates/message_templates/contribution_receipt_text.tpl"}{/capture}
{capture assign=contribution_receipt_html}{fetch file="../xml/templates/message_templates/contribution_receipt_html.tpl"}{/capture} {* FIXME: make it an actual HTML template *}
{capture assign=event_receipt_subj}{fetch file="../xml/templates/message_templates/event_receipt_subj.tpl"}{/capture}
{capture assign=event_receipt_text}{fetch file="../xml/templates/message_templates/event_receipt_text.tpl"}{/capture}
{capture assign=event_receipt_html}{fetch file="../xml/templates/message_templates/event_receipt_html.tpl"}{/capture} {* FIXME: make it an actual HTML template *}

INSERT INTO civicrm_option_value
  (option_group_id,        name,                    label) VALUES
  (@tpl_ogid_contribution, 'contribution_dupalert', '{ts escape="sql"}Contribution Duplicate Organization Alert{/ts}'),
  (@tpl_ogid_contribution, 'contribution_receipt',  '{ts escape="sql"}Contribution Receipt{/ts}'),
  (@tpl_ogid_event,        'event_receipt',         '{ts escape="sql"}Event Receipt{/ts}');

SELECT @tpl_ovid_contribution_dupalert := MAX(id) FROM civicrm_option_value WHERE option_group_id = @tpl_ogid_contribution AND name = 'contribution_dupalert';
SELECT @tpl_ovid_contribution_receipt  := MAX(id) FROM civicrm_option_value WHERE option_group_id = @tpl_ogid_contribution AND name = 'contribution_receipt';
SELECT @tpl_ovid_event_receipt         := MAX(id) FROM civicrm_option_value WHERE option_group_id = @tpl_ogid_event        AND name = 'event_receipt';

INSERT INTO civicrm_msg_template
  (msg_title,                                                         msg_subject,                                     msg_text,                                        msg_html,                                        workflow_id) VALUES
  ('{ts escape="sql"}Contribution Duplicate Organization Alert{/ts}', '{$contribution_dupalert_subj|escape:"quotes"}', '{$contribution_dupalert_text|escape:"quotes"}', '{$contribution_dupalert_html|escape:"quotes"}', @tpl_ovid_contribution_dupalert),
  ('{ts escape="sql"}Contribution Receipt{/ts}',                      '{$contribution_receipt_subj|escape:"quotes"}',  '{$contribution_receipt_text|escape:"quotes"}',  '{$contribution_receipt_html|escape:"quotes"}',  @tpl_ovid_contribution_receipt),
  ('{ts escape="sql"}Event Receipt{/ts}',                             '{$event_receipt_subj|escape:"quotes"}',         '{$event_receipt_text|escape:"quotes"}',         '{$event_receipt_html|escape:"quotes"}',         @tpl_ovid_event_receipt);
