{* Quest Pre-application: Display container for application pages. *}

{edit}
{/edit}

{if $context EQ 'begin'}
  <div id="preapp-content">
  {if ! ( $action & 1024 ) }{* We skip greeting and nav buttons and .js for preview action, but include them for edit and view actions. *}
    <script type="text/javascript" src="{$config->resourceBase}js/Individual.js"></script>
    <table cellpadding=0 cellspacing=0 border=0 id="preapp-content">
        <tr>
            <td class="greeting">Welcome,&nbsp;{$welcome_name}</td>
            <td class="preapp-message" align="center">
             {ts}
               Please note: the application deadline is May 15, 2006. ( * = required field)<BR>You must click 'Save &amp; Continue' to save your changes.
             {/ts}
            </td>           	
            <td nowrap class="save">
            <div class="crm-submit-buttons">
                {$form.buttons.html}
            </div>
            </td>	    	
        </tr>
     </table>
   {/if}
    <table cellpadding=0 cellspacing=0 border=0 id="preapp-content">
        <tr>
          {if ! ( $action & 1024 )}
            <td valign=top nowrap id="preapp-left-nav">
            {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
            {include file="CRM/WizardHeader.tpl}
            <br>
            <div id="help-box">
              <strong>Need Help?</strong><br />
              &nbsp;&nbsp;<a href="javascript:chatW=window.open('http://www.questbridge.org/support/live_support.html','Support','width=600,height=470,resizable=yes'); chatW.focus()">Talk to a Quest team<br>&nbsp;&nbsp;member</A><br />
            </div>
            <div id="help-box">
              <strong>Application Status</strong><br />
              &nbsp;&nbsp;{$taskStatus}
            </div>
            <div id="help-box">
              <strong>Navigation Color Guide</strong><br />
              &nbsp;&nbsp;<IMG SRC="themes/quest/green_box.gif" WIDTH="8" HEIGHT="8"> Section not completed<BR>&nbsp;&nbsp;&nbsp;&nbsp;or section has error(s)
            </div>
	    </td>
        {/if}
        <!--begin main right cell that contains the application-->
        <td valign=top class="rightside">
        {include file="CRM/common/status.tpl"}
        {include file="CRM/Form/body.tpl"}
{/if}

{if $context EQ 'end'}
  {if ! ( $action & 1024 )}
    <div class="crm-submit-buttons">
        {$form.buttons.html}
    </div>
  {/if}
    </td>
    </tr>
    </table>
  {if ! ( $action & 1024 )}
    <table cellpadding=0 cellspacing=0 border=0 id="preapp-content">
        <tr>
           <td class="preapp-message" colspan=2>
             {ts}
               You must click 'Save &amp; Continue' to save your changes.
             {/ts}
           </td>
    </table>
  {/if}
  </div>
  {edit}
    {* Include Javascript to hide and display the appropriate blocks as directed by the php code *}
    {include file="CRM/common/showHide.tpl"}
  {/edit}
{/if}
