-- CRM-4257

SELECT @og_id_pl := max(id) from civicrm_option_group where name = 'participant_listing';

DELETE FROM `civicrm_option_value` WHERE `option_group_id` = @og_id_pl;

 {if $multilingual}
    INSERT INTO civicrm_option_value
      (option_group_id, {foreach from=$locales item=locale}label_{$locale}, description_{$locale}, {/foreach}  value, name, filter, weight, is_reserved, is_active) VALUES
      (@og_id_pl, {foreach from=$locales item=locale}'Name Only', 'CRM_Event_Page_ParticipantListing_Name',{/foreach}  1, 'Name Only', 0, 1, 1, 1),
      (@og_id_pl, {foreach from=$locales item=locale}'Name and Email', 'CRM_Event_Page_ParticipantListing_NameAndEmail',{/foreach}  2, 'Name and Email', 0, 2, 1, 1),
      (@og_id_pl, {foreach from=$locales item=locale}'Name, Status and Register Date', 'CRM_Event_Page_ParticipantListing_NameStatusAndDate',{/foreach}  3, 'Name, Status and Register Date', 0, 3, 1, 1);

 {else}
    INSERT INTO `civicrm_option_value`  
      (`option_group_id`, `label`, `value`, `name`, `filter`, `weight`, `description`, `is_reserved`, `is_active`) VALUES                 
      (@og_id_pl, 'Name Only', 1, 'Name Only', 0, 1, 'CRM_Event_Page_ParticipantListing_Name', 1, 1),
      (@og_id_pl, 'Name and Email', 2, 'Name and Email', 0, 2, 'CRM_Event_Page_ParticipantListing_NameAndEmail', 1, 1),
      (@og_id_pl, 'Name, Status and Register Date', 3, 'Name, Status and Register Date', 0, 3, 'CRM_Event_Page_ParticipantListing_NameStatusAndDate', 1, 1);
 {/if}
