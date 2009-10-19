{* not sure how to define the below in Smarty, so doing it in PHP instead *}
{php}
  $ogNames = array(
    'case'         => 'Message Template Workflow for Cases',
    'contribution' => 'Message Template Workflow for Contributions',
    'event'        => 'Message Template Workflow for Events',
    'friend'       => 'Message Template Workflow for Tell-a-Friend',
    'pledge'       => 'Message Template Workflow for Pledges',
    'uf'           => 'Message Template Workflow for UF',
  );
  $ovNames = array(
    'case' => array(
      'case_activity' => 'Case Activity',
    ),
    'contribution' => array(
      'contribution_additional_info' => 'Contribution Additional Information',
      'contribution_dupalert'        => 'Contribution Duplicate Organization Alert',
      'contribution_offline_receipt' => 'Contribution Offline Receipt',
      'contribution_receipt'         => 'Contribution Receipt',
    ),
    'event' => array(
      'event_offline_receipt' => 'Event Offline Receipt',
      'event_receipt'         => 'Event Receipt',
      'participant_cancelled' => 'Participant Cancelled',
      'participant_confirm'   => 'Participant Confirm',
      'participant_expired'   => 'Participant Expired',
    ),
    'friend' => array(
      'friend' => 'Tell-a-Friend',
    ),
    'pledge' => array(
      'pledge_acknowledge' => 'Pledge Acknowledge',
    ),
    'uf' => array(
      'uf_notify' => 'UF Notify',
    ),
  );
  $this->assign('ogNames',  $ogNames);
  $this->assign('ovNames',  $ovNames);
{/php}

{* FIXME: all ts calls below should SQL-escape the output *}

INSERT INTO civicrm_option_group
  (name,                         label,               description,         is_reserved, is_active) VALUES
  {foreach from=$ogNames key=name item=description name=for_groups}
    ('msg_tpl_workflow_{$name}', '{$description|ts}', '{$description|ts}', 0,           1) {if $smarty.foreach.for_groups.last};{else},{/if}
  {/foreach}

{foreach from=$ogNames key=name item=description}
  SELECT @tpl_ogid_{$name} := MAX(id) FROM civicrm_option_group WHERE name = 'msg_tpl_workflow_{$name}';
{/foreach}

INSERT INTO civicrm_option_value
  (option_group_id,        name,                    label) VALUES
  {foreach from=$ovNames key=gName item=ovs name=for_groups}
    {foreach from=$ovs key=vName item=label name=for_values}
      (@tpl_ogid_{$gName}, '{$vName}', '{$label|ts}') {if $smarty.foreach.for_groups.last and $smarty.foreach.for_values.last};{else},{/if}
    {/foreach}
  {/foreach}

{foreach from=$ovNames key=gName item=ovs}
  {foreach from=$ovs key=vName item=label}
    SELECT @tpl_ovid_{$vName} := MAX(id) FROM civicrm_option_value WHERE option_group_id = @tpl_ogid_{$gName} AND name = '{$vName}';
  {/foreach}
{/foreach}

INSERT INTO civicrm_msg_template
  (msg_title,         msg_subject,                  msg_text,                  msg_html,                  workflow_id) VALUES
  {foreach from=$ovNames key=gName item=ovs name=for_groups}
    {foreach from=$ovs key=vName item=title name=for_values}
      {* FIXME: the paths below will most probably not work outside of bin/setup.sh runs *}
      {* FIXME: the *_html.tpl templates do not have actual HTML yet *}
      {capture assign=subject}{fetch file="../xml/templates/message_templates/`$vName`_subject.tpl"}{/capture}
      {capture assign=text   }{fetch file="../xml/templates/message_templates/`$vName`_text.tpl"   }{/capture}
      {capture assign=html   }{fetch file="../xml/templates/message_templates/`$vName`_html.tpl"   }{/capture}
      ('{$title|ts}', '{$subject|escape:"quotes"}', '{$text|escape:"quotes"}', '{$html|escape:"quotes"}', @tpl_ovid_{$vName}) {if $smarty.foreach.for_groups.last and $smarty.foreach.for_values.last};{else},{/if}
    {/foreach}
  {/foreach}
