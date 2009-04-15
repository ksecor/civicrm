-- CRM-4374

SELECT @og_id_at := max(id) from civicrm_option_group where name = 'activity_type';
SELECT @maxValue := max(value) from civicrm_option_value where option_group_id=@og_id_at;
SELECT @maxWt    := max(weight) from civicrm_option_value where option_group_id=@og_id_at;

 {if $multilingual}
    INSERT INTO civicrm_option_value
      (option_group_id, {foreach from=$locales item=locale}label_{$locale},{/foreach}  value, name, filter, weight, is_reserved, is_active) VALUES
      (@og_id_at, {foreach from=$locales item=locale}'Change Case Start Date',{/foreach} @maxValue + 1, 'Change Case Start Date', 0, @maxWt + 1, 1, 1);
 {else}
    INSERT INTO `civicrm_option_value`  
      (`option_group_id`, `label`, `value`, `name`, `filter`, `weight`, `is_reserved`, `is_active`) VALUES                 
      (@og_id_at, 'Change Case Start Date', @maxValue + 1, 'Change Case Start Date', 0, @maxWt + 1, 1, 1);
 {/if}

