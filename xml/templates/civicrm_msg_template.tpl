{* not sure how to define the below in Smarty, so doing it in PHP instead *}
{php}
  $ogNames = array(
    'case'         => ts('Message Template Workflow for Cases',          array('escape' => 'sql')),
    'contribution' => ts('Message Template Workflow for Contributions',  array('escape' => 'sql')),
    'event'        => ts('Message Template Workflow for Events',         array('escape' => 'sql')),
    'friend'       => ts('Message Template Workflow for Tell-a-Friend',  array('escape' => 'sql')),
    'meta'         => ts('Message Template Workflow for Meta Templates', array('escape' => 'sql')),
    'pledge'       => ts('Message Template Workflow for Pledges',        array('escape' => 'sql')),
    'uf'           => ts('Message Template Workflow for Profiles',       array('escape' => 'sql')),
  );
  $ovNames = array(
    'case' => array(
      'case_activity' => ts('Cases - Send Copy of an Activity', array('escape' => 'sql')),
    ),
    'contribution' => array(
      'contribution_additional_info'  => ts('Contributions - Additional Information',                         array('escape' => 'sql')),
      'contribution_dupalert'         => ts('Contributions - Duplicate Organization Alert',                   array('escape' => 'sql')),
      'contribution_offline_receipt'  => ts('Contributions and Memberships - Receipt (off-line)',             array('escape' => 'sql')),
      'contribution_receipt'          => ts('Contributions and Memberships - Receipt (on-line)',              array('escape' => 'sql')),
      'contribution_recurring_notify' => ts('Contributions - Recurring Start and End Notification',           array('escape' => 'sql')),
      'pcp_notify'                    => ts('Personal Campaign Pages - Admin Notification',                   array('escape' => 'sql')),
      'pcp_status_change'             => ts('Personal Campaign Pages - Supporter Status Change Notification', array('escape' => 'sql')),
      'pcp_supporter_notify'          => ts('Personal Campaign Pages - Supporter Welcome',                    array('escape' => 'sql')),
    ),
    'event' => array(
      'event_offline_receipt' => ts('Events - Registration Confirmation and Receipt (off-line)', array('escape' => 'sql')),
      'event_receipt'         => ts('Events - Registration Confirmation and Receipt (on-line)',  array('escape' => 'sql')),
      'participant_cancelled' => ts('Events - Registration Cancellation Notice',                 array('escape' => 'sql')),
      'participant_confirm'   => ts('Events - Registration Confirmation Invite',                 array('escape' => 'sql')),
      'participant_expired'   => ts('Events - Pending Registration Expiration Notice',           array('escape' => 'sql')),
    ),
    'friend' => array(
      'friend' => 'Tell-a-Friend Email',
    ),
    'meta' => array(
      'test_preview' => ts('Test-drive - Receipt Header', array('escape' => 'sql')),
    ),
    'pledge' => array(
      'pledge_acknowledge' => ts('Pledges - Acknowledgement',  array('escape' => 'sql')),
      'pledge_reminder'    => ts('Pledges - Payment Reminder', array('escape' => 'sql')),
    ),
    'uf' => array(
      'uf_notify' => ts('Profiles - Admin Notification', array('escape' => 'sql')),
    ),
  );
  $this->assign('ogNames',  $ogNames);
  $this->assign('ovNames',  $ovNames);
{/php}

INSERT INTO civicrm_option_group
  (name,                         label,            description,      is_reserved, is_active) VALUES
  {foreach from=$ogNames key=name item=description name=for_groups}
    ('msg_tpl_workflow_{$name}', '{$description}', '{$description}', 0,           1) {if $smarty.foreach.for_groups.last};{else},{/if}
  {/foreach}

{foreach from=$ogNames key=name item=description}
  SELECT @tpl_ogid_{$name} := MAX(id) FROM civicrm_option_group WHERE name = 'msg_tpl_workflow_{$name}';
{/foreach}

INSERT INTO civicrm_option_value
  (option_group_id,        name,       label,   value,  weight ) VALUES
  {foreach from=$ovNames key=gName item=ovs name=for_groups}
    {foreach from=$ovs key=vName item=label name=for_values}
      (@tpl_ogid_{$gName}, '{$vName}', '{$label}', {$smarty.foreach.for_values.iteration}, {$smarty.foreach.for_values.iteration}) {if $smarty.foreach.for_groups.last and $smarty.foreach.for_values.last};{else},{/if}
    {/foreach}
  {/foreach}

{foreach from=$ovNames key=gName item=ovs}
  {foreach from=$ovs key=vName item=label}
    SELECT @tpl_ovid_{$vName} := MAX(id) FROM civicrm_option_value WHERE option_group_id = @tpl_ogid_{$gName} AND name = '{$vName}';
  {/foreach}
{/foreach}

INSERT INTO civicrm_msg_template
  (msg_title,      msg_subject,                  msg_text,                  msg_html,                  workflow_id,        is_default, is_reserved) VALUES
  {foreach from=$ovNames key=gName item=ovs name=for_groups}
    {foreach from=$ovs key=vName item=title name=for_values}
      {* FIXME: the paths below will most probably not work outside of bin/setup.sh runs *}
      {* FIXME: the *_html.tpl templates do not have actual HTML yet *}
      {fetch assign=subject file="../xml/templates/message_templates/`$vName`_subject.tpl"}
      {fetch assign=text    file="../xml/templates/message_templates/`$vName`_text.tpl"}
      {fetch assign=html    file="../xml/templates/message_templates/`$vName`_html.tpl"}
      ('{$title}', '{$subject|escape:"quotes"}', '{$text|escape:"quotes"}', '{$html|escape:"quotes"}', @tpl_ovid_{$vName}, 1,          0),
      ('{$title}', '{$subject|escape:"quotes"}', '{$text|escape:"quotes"}', '{$html|escape:"quotes"}', @tpl_ovid_{$vName}, 0,          1) {if $smarty.foreach.for_groups.last and $smarty.foreach.for_values.last};{else},{/if}
    {/foreach}
  {/foreach}
