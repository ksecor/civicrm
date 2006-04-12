{* Quest Pre-application: Display container for application pages. *}

{* Including the javascript source code from the Individual.js *}
 <script type="text/javascript" src="{$config->resourceBase}js/Individual.js"></script>

{if $context EQ 'begin'}
  {if $superAction != 1024}
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
             {ts}
               Please note: the application deadline is May 15, 2006. ( * = required field)<BR>You must click 'Save &amp; Continue' to save your changes.
             {/ts}
           </td>
        </tr>
     </table>
   {/if}
    <table cellpadding=0 cellspacing=0 border=0 id="preapp-content">
        <tr>
          {if $superAction != 1024}
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
              &nbsp;&nbsp;<IMG SRC="http://questbridge.org/images/green_box.gif" WIDTH="8" HEIGHT="8"> Section not completed<BR>&nbsp;&nbsp;&nbsp;&nbsp;or section has error(s)
            </div>
	    </td>
          {/if}
        <!--begin main right cell that contains the application-->
        <td valign=top class="rightside">
        {include file="CRM/common/status.tpl"}
        {include file="CRM/Form/body.tpl"}
{/if}

{if $context EQ 'end'}
  {if $superAction != 1024}
    <div class="crm-submit-buttons">
        {$form.buttons.html}
    </div>
  {/if}
    </td>
    </tr>
    </table>
  {if $superAction != 1024}
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
  {if $superAction != 1024}
    {* Include Javascript to hide and display the appropriate blocks as directed by the php code *}
    {include file="CRM/common/showHide.tpl"}
  {/if}
{/if}
