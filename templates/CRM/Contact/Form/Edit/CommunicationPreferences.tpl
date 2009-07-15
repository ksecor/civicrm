{* This file provides the plugin for the communication preferences in all the three types of contact *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

<h3 class="head"> 
    <span class="ui-icon ui-icon-triangle-1-e"></span><a href="#">{ts}{$title}{/ts}</a>
</h3>
<div id="commPrefs" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
    <table class="form-layout-compressed" >
        <tr>
           {if $form.postal_greeting_id}
                <td>{$form.postal_greeting_id.label} &nbsp; </td>
                <td id="postal_greeting_id_label" style="display:none;">{$form.postal_greeting_custom.label}</td>
            {/if}
            {if $form.addressee_id}
                <td>{$form.addressee_id.label} &nbsp; </td>
                <td id="addressee_id_label" style="display:none;">{$form.addressee_custom.label}</td>
            {/if}
            {if $form.email_greeting_id}
                <td>{$form.email_greeting_id.label} &nbsp; </td>
                <td id="email_greeting_id_label" style="display:none;">{$form.email_greeting_custom.label}</td>
            {/if}
       </tr>
       <tr id="greetings">
             {if $form.postal_greeting_id}
                <td>{$form.postal_greeting_id.html|crmReplace:class:big}</td>
                <td id="postal_greeting_id_html" style="display:none;">{$form.postal_greeting_custom.html|crmReplace:class:big}</td>
            {/if}
            {if $form.addressee_id}
                <td>{$form.addressee_id.html|crmReplace:class:big}</td>
                <td id="addressee_id_html" style="display:none;">{$form.addressee_custom.html|crmReplace:class:big}</td>
            {/if}
            {if $form.email_greeting_id}
                <td>{$form.email_greeting_id.html|crmReplace:class:big}</td>
                <td id="email_greeting_id_html" style="display:none;">{$form.email_greeting_custom.html|crmReplace:class:big}</td>
            {/if}
        </tr>
        <tr><td colspan="5" id="greeting_display" style="font-size:10px;"></td></tr>
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
function showCustomized(element){
    var eleHtml  = 'td#'+element+'_html';
    var eleLabel = 'td#'+element+'_label';
    var selText  = cj('#'+element+' :selected').text();
    if (  selText == 'Customized' ) { 
        cj( eleHtml+","+eleLabel).toggle( );
    } else {
        cj( eleHtml+","+eleLabel).hide( );
        if ( selText != '- select -' ) {
            //fixme for showing token to compiled string
            cj('#greeting_display').html('<strong>Display :: </strong>'+cj('#'+element+' :selected').text()).show().fadeOut(5000);
        }
    }
}
cj('tr#greetings td').each(function() { 
	element = cj(this).children().attr('id');
	if ( cj( '#'+element +' :selected' ).text() == 'Customized' ){
		showCustomized( element );
	}
});
</script>
{/literal}