-- CRM-4374

SELECT @og_id_at   := max(id) from civicrm_option_group where name = 'activity_type';
SELECT @max_val_at := max(value) from civicrm_option_value where option_group_id=@og_id_at;
SELECT @max_wt_at  := max(weight) from civicrm_option_value where option_group_id=@og_id_at;

SELECT @og_id_mp   := max(id) from civicrm_option_group where name = 'mail_protocol';
SELECT @max_val_mp := max(value) from civicrm_option_value where option_group_id=@og_id_mp;
SELECT @max_wt_mp  := max(weight) from civicrm_option_value where option_group_id=@og_id_mp;

 {if $multilingual}
    INSERT INTO civicrm_option_value
      (option_group_id, {foreach from=$locales item=locale}label_{$locale},{/foreach}  value, name, filter, weight, is_reserved, is_active) VALUES
      (@og_id_at, {foreach from=$locales item=locale}'Change Case Start Date',{/foreach} @max_val_at + 1, 'Change Case Start Date', 0, @max_wt_at + 1, 1, 1),
      (@og_id_mp, {foreach from=$locales item=locale}'Localdir',{/foreach} @max_val_mp + 1, 'Localdir', 0, @max_wt_mp + 1, 1, 1);
 {else}
    INSERT INTO `civicrm_option_value`  
      (`option_group_id`, `label`, `value`, `name`, `filter`, `weight`, `is_reserved`, `is_active`) VALUES                 
      (@og_id_at, 'Change Case Start Date', @max_val_at + 1, 'Change Case Start Date', 0, @max_wt_at + 1, 1, 1),
      (@og_id_mp, 'Localdir',               @max_val_mp + 1, 'Localdir',               0, @max_wt_mp + 1, 1, 1);
 {/if}
