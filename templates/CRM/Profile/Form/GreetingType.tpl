{assign var="customGreeting" value=$n|cat:"_custom"}
<span>{$form.$n.html|crmReplace:class:big}</span>&nbsp;<span id="{$customGreeting}_html" class="hiddenElement">{$form.$customGreeting.html|crmReplace:class:big}</span>

<script type="text/javascript">
var fieldName = '{$n}';
{literal}
cj( "#" + fieldName ).change( function( ) {
    var fldName = cj(this).attr( 'id' );
    showCustom( fldName, cj(this).val( ) );
});

showCustom( fieldName, cj( "#" + fieldName ).val( ) );
function showCustom( fldName, value ) {
    if ( value == 4 ) {
        cj( "#" + fldName + "_custom_html").show( );
    } else {
        cj( "#" + fldName + "_custom_html").hide( );
        cj( "#" + fldName + "_custom" ).val('');
    }
}
{/literal}
</script>