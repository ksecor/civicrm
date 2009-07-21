{* This file provides the plugin for the communication preferences in all the three types of contact *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

<h3 class="head"> 
    <span class="ui-icon ui-icon-triangle-1-e"></span><a href="#">{ts}{$title}{/ts}</a>
</h3>
<div id="commPrefs" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
    <table class="form-layout-compressed" >
        <tr>
           {if $form.postal_greeting_id}
                <td>{$form.postal_greeting_id.label}</td>
            {/if}
            {if $form.addressee_id}
                <td>{$form.addressee_id.label}</td>
            {/if}
            {if $form.email_greeting_id}
                <td>{$form.email_greeting_id.label}</td>
            {/if}
       </tr>
       <tr>
            {if $form.postal_greeting_id}
                <td>
                    <span id="postal_greeting" {if $postal_greeting_display and $action eq 2} class="hiddenElement"{/if}>{$form.postal_greeting_id.html|crmReplace:class:big}</span>
                    {if $postal_greeting_display and $action eq 2}
                        <span id='postal_greeting_display'>{$postal_greeting_display}&nbsp;<a href="#" onclick="showGreeting('postal_greeting');return false;"><img src="{$config->resourceBase}i/edit.png" border="0" title="{ts}Edit{/ts}"></a></span>
                    {/if}
                </td>
            {/if}
            {if $form.addressee_id}
                <td>
                    <span id="addressee" {if $addressee_display and $action eq 2} class="hiddenElement"{/if}>{$form.addressee_id.html|crmReplace:class:big}</span>
                    {if $addressee_display and $action eq 2}
                        <span id='addressee_display'>{$addressee_display}&nbsp;<a href="#" onclick="showGreeting('addressee');return false;"><img src="{$config->resourceBase}i/edit.png" border="0" title="{ts}Edit{/ts}"></a></span>
                    {/if}
                </td>
            {/if}
            {if $form.email_greeting_id}
                <td>
                    <span id="email_greeting" {if $email_greeting_display and $action eq 2} class="hiddenElement"{/if}>{$form.email_greeting_id.html|crmReplace:class:big}</span>
                    {if $email_greeting_display and $action eq 2}
                        <span id='email_greeting_display'>{$email_greeting_display}&nbsp;<a href="#" onclick="showGreeting('email_greeting');return false;"><img src="{$config->resourceBase}i/edit.png" border="0" title="{ts}Edit{/ts}"></a></span>
                    {/if}
                </td>
            {/if}
        </tr>
        <tr id="greetings1" class="hiddenElement">
             {if $form.postal_greeting_custom}
                 <td><span id="postal_greeting_id_label" class="hiddenElement">{$form.postal_greeting_custom.label}</span></td>
             {/if}
             {if $form.addressee_custom}
                 <td><span id="addressee_id_label" class="hiddenElement">{$form.addressee_custom.label}</span></td>
             {/if}
             {if $form.email_greeting_custom}
                 <td><span id="email_greeting_id_label" class="hiddenElement">{$form.email_greeting_custom.label}</span></td>
             {/if}
        </tr>
        <tr id="greetings2" class="hiddenElement">
              {if $form.postal_greeting_custom}
                 <td><span id="postal_greeting_id_html" class="hiddenElement">{$form.postal_greeting_custom.html|crmReplace:class:big}</span></td>
             {/if}
             {if $form.addressee_custom}
                 <td><span id="addressee_id_html" class="hiddenElement">{$form.addressee_custom.html|crmReplace:class:big}</span></td>
             {/if}
             {if $form.email_greeting_custom}
                 <td><span id="email_greeting_id_html" class="hiddenElement">{$form.email_greeting_custom.html|crmReplace:class:big}</span></td>
             {/if}
        </tr>
        <tr>
            {foreach key=key item=item from=$commPreference}
              <td>  
                 {$form.$key.label}{help id="id-$key"}
                 {foreach key=k item=i from=$item}
                  <br />{$form.$key.$k.html}
                 {/foreach}
              </td>
            {/foreach}
        </tr>
        <tr>
            <td>{$form.is_opt_out.html} {$form.is_opt_out.label} {help id="id-optOut"}</td>
            <td>{$form.preferred_mail_format.label} &nbsp;
                {$form.preferred_mail_format.html} {help id="id-emailFormat"}
            </td>

        </tr>
    </table>
</div>
{literal}
<script type="text/javascript">
cj( function( ) {
    var fields = new Array( 'postal_greeting', 'addressee', 'email_greeting');
    for ( var i = 0; i < 3; i++ ) {
        cj( "#" + fields[i] + "_id").change( function( ) {
            var fldName = cj(this).attr( 'id' );
            if ( cj(this).val( ) == 4 ) {
                cj("#greetings1").show( );
                cj("#greetings2").show( );
                cj( "#" + fldName + "_html").show( );
                cj( "#" + fldName + "_label").show( );
            } else {
                cj( "#" + fldName + "_html").hide( );
                cj( "#" + fldName + "_label").hide( );
            }
        });
    }          
});

function showGreeting( element ) {
    cj("#" + element ).show( );
    cj("#" + element + '_display' ).hide( );
    
    // TO DO fix for custom greeting
    var fldName = '#' + element + '_id';
    if ( cj( fldName ).val( ) == 4 ) {
        cj("#greetings1").show( );
        cj("#greetings2").show( );
        cj( fldName + "_html").show( );
        cj( fldName + "_label").show( );
    }
}

</script>
{/literal}