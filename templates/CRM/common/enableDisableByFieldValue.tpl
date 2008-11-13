{* This included tpl disables and enables the appropriate blocks based on trigger values in specified field(s) *}

<script type="text/javascript">
    var trigger_field_id = '{$trigger_field_id}';
    var trigger_value = '{$trigger_value}';
    var target_element_id = '{$target_element_id}';
    var target_element_type = '{$target_element_type}';
    var field_type  = '{$field_type}';
    var invert = {$invert};

    enableDisableByValue(trigger_field_id, trigger_value, target_element_id, target_element_type, field_type, invert);

</script>  
