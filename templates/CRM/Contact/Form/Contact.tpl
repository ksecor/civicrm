{* This form is for Contact Add/Edit interface *}
<div class="crm-submit-buttons">
   {$form.buttons.html}
</div>
<br/>
<div class="accordion ui-accordion ui-widget ui-helper-reset">
    <h3 class="head"> 
        <span class="ui-icon ui-icon-triangle-1-e" id='contact'></span><a href="#">{ts}Contact Details{/ts}</a>
    </h3>
    <div id="contact-details">
        {include file="CRM/Contact/Form/Edit/$contactType.tpl"}
        <br/>
        <table class="form-layout-compressed">
            {foreach from=$blocks item="status" key="block"}
                {if $status }
                    {include file="CRM/Contact/Form/Edit/$block.tpl"}
                {/if}
            {/foreach}            
        </table>
    </div>
    
    {foreach from = $editOptions item = "title" key="name"}
            {include file="CRM/Contact/Form/Edit/$name.tpl"}
    {/foreach}
    
</div>
<br/>
<div class="crm-submit-buttons">
   {$form.buttons.html}
</div>

{literal}
<script type="text/javascript" >
cj(function( ) {
    cj('.accordion .head').addClass( "ui-accordion-header ui-helper-reset ui-state-default ui-corner-all");

    cj('.accordion .head').hover( function( ) { 
        cj(this).addClass( "ui-state-hover");
    }, function() { 
        cj(this).removeClass( "ui-state-hover");
    }).bind('click', function( ) { 
        var checkClass = cj(this).find('span').attr( 'class' );
        var len        = checkClass.length;
        if ( checkClass.substring( len - 1, len ) == 's' ) {
            cj(this).find('span').removeClass( ).addClass('ui-icon ui-icon-triangle-1-e');
        } else {
            cj(this).find('span').removeClass( ).addClass('ui-icon ui-icon-triangle-1-s');
        }
        cj(this).next( ).toggle('blind'); return false; 
    }).next( ).hide( );
    
    cj('span#contact').removeClass( ).addClass('ui-icon ui-icon-triangle-1-s');
    cj("#contact-details").show( );
});
</script>
{/literal}