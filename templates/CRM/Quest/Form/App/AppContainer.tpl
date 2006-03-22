{* Quest Pre-application: Display container for application pages. *}

{* Including the javascript source code from the Individual.js *}
 <script type="text/javascript" src="{$config->resourceBase}js/Individual.js"></script>

{if $context EQ 'begin'}
    <div id="content">
    <table cellpadding=0 cellspacing=0 border=0 id="content">
        <tr>
            <td class="greeting">
            	Welcome,&nbsp;Michael</td>
            <td class="save">
            <div class="crm-submit-buttons">
                {$form.buttons.html}
            </div>	    	
        </tr>
        <tr>
        	<td colspan=2>
         		<hr size=0></td>
        </tr>
        <tr>
         	<td width="1%" valign=top nowrap>
            {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
            {include file="CRM/WizardHeader.tpl}
            <br>
			</td>

        <!--begin main right cell that contains the application-->
        <td valign=top align=center class="rightside">
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
