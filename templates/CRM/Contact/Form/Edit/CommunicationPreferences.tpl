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
                <td>{$form.postal_greeting_id.html|crmReplace:class:big}</td>
            {/if}
            {if $form.addressee_id}
                <td>{$form.addressee_id.html|crmReplace:class:big}</td>
            {/if}
            {if $form.email_greeting_id}
                <td>{$form.email_greeting_id.html|crmReplace:class:big}</td>
            {/if}
        </tr>
         <tr>
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
        <tr id="greetings" class="hiddenElement">
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
                cj("#greetings").show( );
                cj( "#" + fldName + "_html").show( );
                cj( "#" + fldName + "_label").show( );
            } else {
                cj( "#" + fldName + "_html").hide( );
                cj( "#" + fldName + "_label").hide( );
            }
        });
    }
});
</script>
{/literal}