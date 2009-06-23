{*this is included inside the table*}
{assign var=relativeName   value=$fieldName|cat:"_relative"}
<td >{$form.$relativeName.html}</td>
<td>   
    <span id="absolute_{$relativeName}"> 
        {assign var=fromName   value=$fieldName|cat:"_from"}
        {$form.$fromName.label}&nbsp;{$form.$fromName.html} &nbsp; 
        {include file="CRM/common/calendar/desc.tpl" trigger=trigger_$fromName} 
        {include file="CRM/common/calendar/body.tpl" dateVar=$fromName startDate=startYear endDate=endYear offset=5 trigger=trigger_$fromName}
    <br/>               
        {assign var=toName   value=$fieldName|cat:"_to"}&nbsp;&nbsp;&nbsp;&nbsp;
        {$form.$toName.label}&nbsp;{$form.$toName.html} &nbsp; 
        {include file="CRM/common/calendar/desc.tpl" trigger=trigger_$toName} 
        {include file="CRM/common/calendar/body.tpl" dateVar=$toName startDate=startYear endDate=endYear offset=5 trigger=trigger_$toName}
    </span>   
            
</td>
{literal}
<script type="text/javascript">
    var val       = document.getElementById("{/literal}{$relativeName}{literal}").value;
    var fieldName = "{/literal}{$relativeName}{literal}";
    showAbsoluteRange( val, fieldName );

    function showAbsoluteRange( val, fieldName ) {
        if ( val == "0" ) {
            cj('#absolute_'+ fieldName).show();
        } else {
            cj('#absolute_'+ fieldName).hide();
        }
    }
</script>
{/literal}        
