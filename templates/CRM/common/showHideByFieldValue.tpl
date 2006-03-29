{* This included tpl hides and displays the appropriate blocks based on trigger values in specified field(s) *}

<script type="text/javascript">
    var trigger_field_id = '{$trigger_field_id}';
    var trigger_value = '{$trigger_value}';
    var target_element_id = '{$target_element_id}';
    var target_element_type = '{$target_element_type}';

    showHideByValue(trigger_field_id, trigger_value, target_element_id, target_element_type);

</script>  
