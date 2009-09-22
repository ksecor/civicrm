{literal}
<script type="text/javascript" >
var text_message = null;
var html_message = null;
var isMailing    = false;

{/literal}
{if $form.formName eq 'MessageTemplates'}
    {literal}
    text_message = "msg_text";
    html_message = "msg_html";
    {/literal}
{else}
    {literal}
    text_message = "text_message";
    html_message = "html_message";
    isMailing    = true;
    {/literal}
{/if}

{if $templateSelected}
    {literal}
    if ( document.getElementsByName("saveTemplate")[0].checked ) {
        document.getElementById('template').selectedIndex = {/literal}{$templateSelected}{literal};  	
    }
    {/literal}
{/if}
{literal}

var editor = {/literal}"{$editor}"{literal};
function loadEditor()
{
    var msg =  {/literal}"{$htmlContent}"{literal};
    if (msg) {
        if ( editor == "fckeditor" ) {
            oEditor = FCKeditorAPI.GetInstance(html_message);
            oEditor.SetHTML( msg );
        }
    }
}

function showSaveUpdateChkBox()
{
    if ( document.getElementById('template') == null ) {
        if (document.getElementsByName("saveTemplate")[0].checked){
            document.getElementById("saveDetails").style.display = "block";
            document.getElementById("editMessageDetails").style.display = "block";
        } else {
            document.getElementById("saveDetails").style.display = "none";
            document.getElementById("editMessageDetails").style.display = "none";
        }
        return;
    }

    if ( document.getElementsByName("saveTemplate")[0].checked && document.getElementsByName("updateTemplate")[0].checked == false  ) {
        document.getElementById("updateDetails").style.display = "none";
    } else if ( document.getElementsByName("saveTemplate")[0].checked && document.getElementsByName("updateTemplate")[0].checked ){
        document.getElementById("editMessageDetails").style.display = "block";	
        document.getElementById("saveDetails").style.display = "block";	
    } else if ( document.getElementsByName("saveTemplate")[0].checked == false && document.getElementsByName("updateTemplate")[0].checked ){
        document.getElementById("saveDetails").style.display = "none";
        document.getElementById("editMessageDetails").style.display = "block";
    } else {
        document.getElementById("saveDetails").style.display = "none";
        document.getElementById("editMessageDetails").style.display = "none";
    }

}

function selectValue( val ) {
    if ( !val ) {
        document.getElementById(text_message).value ="";
        document.getElementById("subject").value ="";
        if ( editor == "fckeditor" ) {
            oEditor = FCKeditorAPI.GetInstance(html_message);
            oEditor.SetHTML('');
        } else if ( editor == "tinymce" ) {
            cj('#' + html_message).html('');
        } else {	
            document.getElementById(html_message).value = '' ;
        }
        return;
    }

    var dataUrl = {/literal}"{crmURL p='civicrm/ajax/template' h=0 }"{literal};

    cj.post( dataUrl, {tid: val}, function( data ) {
        cj("#subject").val( data.subject );

        if ( data.msg_text ) {      
            cj("#"+text_message).val( data.msg_text );
            cj("div.text").show();
            cj(".head").find('span').removeClass().addClass('ui-icon ui-icon-triangle-1-s');
            cj("#helptext").show(); 
        } else {
            cj("#"+text_message).val("");
        }

        var html_body  = "";
        if (  data.msg_html ) {
            html_body = data.msg_html;
        }

        if ( editor == "fckeditor" ) {
            oEditor = FCKeditorAPI.GetInstance(html_message);
            oEditor.SetHTML( html_body );
        } else if ( editor == "tinymce" ) {
            cj('#'+ html_message).html( html_body );
        } else {	
            cj("#"+ html_message).val( html_body );
        }

        }, 'json');    
    }

 if ( isMailing ) { 
     document.getElementById("editMessageDetails").style.display = "block";
    

    function verify( select )
    {
        if ( document.getElementsByName("saveTemplate")[0].checked  == false ) {
            document.getElementById("saveDetails").style.display = "none";
        }
        document.getElementById("editMessageDetails").style.display = "block";

        var templateExists = true;
        if ( document.getElementById('template') == null ) {
            templateExists = false;
        }

        if ( templateExists && document.getElementById('template').value ) {
            document.getElementById("updateDetails").style.display = '';
        } else {
            document.getElementById("updateDetails").style.display = 'none';
        }

        document.getElementById("saveTemplateName").disabled = false;
    }

    function showSaveDetails(chkbox) 
    {
        if (chkbox.checked) {
            document.getElementById("saveDetails").style.display = "block";
            document.getElementById("saveTemplateName").disabled = false;
        } else {
            document.getElementById("saveDetails").style.display = "none";
            document.getElementById("saveTemplateName").disabled = true;
        }
    }

    showSaveUpdateChkBox();

    {/literal}
    {if $editor eq "fckeditor"}
        {literal}
        function FCKeditor_OnComplete( editorInstance )
        {
            oEditor = FCKeditorAPI.GetInstance(html_message);
            oEditor.SetHTML( {/literal}'{$message_html}'{literal});
            oEditor.BaseHref = '' ;
            oEditor.UserFilesPath = '' ; 
            loadEditor();
	        oEditor.Events.AttachEvent( 'OnFocus',verify ) ;
            
        }
        {/literal}
    {else if $editor eq "tinymce"}
        {literal}
        cj( function( ) {
            cj("#"+ html_message).keypress( function( ) {
               if ( isMailing ) { 
                    verify();
               }
               
            });
        });
        {/literal}
    {/if}
    {literal}
 }

    function tokenReplText ( )
    {
        var token     = cj("#token1").val( )[0];
        var msg       = cj("#"+ text_message).val( );
        var cursorlen = document.getElementById(text_message).selectionStart;
        var textlen   = msg.length;
        document.getElementById(text_message).value = msg.substring(0, cursorlen) + token + msg.substring(cursorlen, textlen);
        var cursorPos = (cursorlen + token.length);
        document.getElementById(text_message).selectionStart = cursorPos;
        document.getElementById(text_message).selectionEnd   = cursorPos;
        document.getElementById(text_message).focus();
        if ( isMailing ) { 
             verify();
        }
    }

    function tokenReplHtml ( )
    {
        var token2     = cj("#token2").val( )[0];
        var editor = {/literal}"{$editor}"{literal};
        if ( editor == "tinymce" ) {
            cj('#'+ html_message).tinymce().execCommand('mceInsertContent',false, token2);
        } else if ( editor == "fckeditor" ) {
            oEditor = FCKeditorAPI.GetInstance(html_message);
            oEditor.InsertHtml( token2.toString() );
        } else {
            var msg       = document.getElementById(html_message).value;
            var cursorlen = document.getElementById(html_message).selectionStart;
            var textlen   = msg.length;
            document.getElementById(html_message).value = msg.substring(0, cursorlen) + token2 + msg.substring(cursorlen, textlen);
            var cursorPos = (cursorlen + token2.length);
            document.getElementById(html_message).selectionStart = cursorPos;
            document.getElementById(html_message).selectionEnd   = cursorPos;
            document.getElementById(html_message).focus();
        }

        if ( isMailing ) { 
             verify();
        }
    }

    cj(function() {
        cj('.accordion .head').addClass( "ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ");

        // restructuring css as per jQuery tab width
        cj('.ui-state-default, .ui-widget-content .ui-state-default').css( 'width', '95%' );
        cj('.resizable-textarea textarea').css( 'width', '99%' );
        cj('.grippie').css( 'margin-right', '3px');
        cj('.accordion .head').hover( function() { cj(this).addClass( "ui-state-hover");
        }, function() { cj(this).removeClass( "ui-state-hover");
    }).bind('click', function() { 
        var checkClass = cj(this).find('span').attr( 'class' );
        var len        = checkClass.length;
        if ( checkClass.substring( len - 1, len ) == 's' ) {
            cj(this).find('span').removeClass().addClass('ui-icon ui-icon-triangle-1-e');
            cj("span#help"+cj(this).find('span').attr('id')).hide();
        } else {
            cj(this).find('span').removeClass().addClass('ui-icon ui-icon-triangle-1-s');
            cj("span#help"+cj(this).find('span').attr('id')).show();
        }
        cj(this).next().toggle(); return false; }).next().hide();
        cj('span#html').removeClass().addClass('ui-icon ui-icon-triangle-1-s');
        cj("div.html").show();
       
        if ( !isMailing ) {
           cj("div.text").show();
        }   
    });

    {/literal}{include file="CRM/common/Filter.tpl"}{literal}
    function showToken(element, id ) {
	initFilter(id);
	cj("#token"+id).css({"width":"290px", "size":"8"});
	var tokenTitle = {/literal}'{ts}Select Token{/ts}'{literal};
        cj("#token"+element ).show( ).dialog({
            title       : tokenTitle,
            modal       : true,
            width       : '310px',
            resizable   : false,
            bgiframe    : false,
            overlay     : { opacity: 0.5, background: "black" },
            beforeclose : function(event, ui) { cj(this).dialog("destroy"); },
            buttons     : { 
                "Done": function() { 
                    cj(this).dialog("close");
                        //focus on editor/textarea after token selection     
                        if (element == 'Text') {
                            cj('#' + text_message).focus();
                        } else if (element == 'Html' ) {
                            switch ({/literal}"{$editor}"{literal}) {
                                case 'fckeditor': { FCKeditorAPI.GetInstance(html_message).Focus(); break;}
                                case 'tinymce'  : { tinyMCE.get(html_message).focus(); break; } 
                                default         : { cj("#"+ html_message).focus(); break; } 
                        }
                    }
                }
            }
        });
        return false;
    }

</script>
{/literal}