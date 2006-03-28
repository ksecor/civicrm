{* Quest Pre-application: Display container for application pages. *}

{* Including the javascript source code from the Individual.js *}
 <script type="text/javascript" src="{$config->resourceBase}js/Individual.js"></script>

{if $context EQ 'begin'}
    <div id="preapp-content">
    <table cellpadding=0 cellspacing=0 border=0 id="preapp-content">
        <tr>
            <td class="greeting">
            	Welcome,&nbsp;{$welcome_name}</td>
            <td class="save">
            <div class="crm-submit-buttons">
                {$form.buttons.html}
            </div>
            </td>	    	
        </tr>
        <tr>
           <td class="preapp-message" colspan=2>
             {ts}Please note: the application deadline is May 15, 2006{/ts}
           </td>
        </tr>
        <tr>
         	<td width="1%" valign=top nowrap id="preapp-left-nav">
            {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
            {include file="CRM/WizardHeader.tpl}
            <br>
			</td>

        <!--begin main right cell that contains the application-->
        <td valign=top align=center class="rightside">
        {include file="CRM/Form/body.tpl"}

{/if}

{if $context EQ 'end'}
    <div class="crm-submit-buttons">
        {$form.buttons.html}
    </div>
    </td>
    </tr>
    </table>
    </div>
 <script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
 </script>
{/if}
