{* Quest College Match Application: Display container for application pages. *}
{if $context EQ 'begin'}
  <div id="app-content">
  {if ! ( $action & 1024 ) }{* We skip greeting and nav buttons and .js for preview action, but include them for edit and view actions. *}
    <script type="text/javascript" src="{$config->resourceBase}js/Individual.js"></script>
    <table cellpadding=0 cellspacing=0 border=0 id="app-content">
        <tr>
            <td class="greeting">{$welcome_name}</td>
            <td class="app-message" align="center">
             {ts}
                Note: The application deadline is Oct 1, 2006. ( * = required field)<BR>You must click 'Save &amp; Continue' to save your changes.
             {/ts}
            </td>           	
            <td nowrap class="save">
            <div class="crm-submit-buttons">
                {$form.buttons.html}
                {if $userContext} 
                    <div>
                        <a href="{$userContext}">&raquo; {ts}Back to Branner{/ts}</a>   
                    </div>
                {/if}
            </div>
            </td>	    	
        </tr>
     </table>
   {/if}
    <table cellpadding=0 cellspacing=0 border=0 id="app-content">
        <tr>
          {if ! ( $action & 1024 )}
            <td valign=top nowrap id="app-left-nav">
            {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
            {include file="CRM/WizardHeader.tpl}
            <br>
            <div id="help-box">
              <strong>Need Help?</strong><br />
              <A HREF="http://www.questbridge.org/recruitment/support_student_view.html" TARGET="_blank">Chat</A> with a Quest staff member.<br />
<!--              <A HREF="mailto:techsupport@questbridge.org">Email us</A> for help with the<BR>application.<br /> //-->
            </div>
<!--
            <div id="help-box">
              <strong>Need Help?</strong><br />
              &nbsp;&nbsp;<a href="javascript:chatW=window.open('http://www.questbridge.org/support/live_support.html','Support','width=600,height=470,resizable=yes'); chatW.focus()">Talk to a Quest team<br>&nbsp;&nbsp;member</A><br />
            </div>
//-->
            <div id="help-box">
              <strong>Application Status</strong><br />
              &nbsp;&nbsp;{$taskStatus}
            </div>
            <div id="help-box">
              <strong>Navigation Color Guide</strong><br />
              <B>Green</B>: Section not<BR>completed or section<br>has error(s)<BR>
              <B>Blue</B>: Section has been<BR>completed or is available<BR>for editing<BR>
              <B>Gray</B>: Section is not<br>available for editing<BR>
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
        {if $userContext} 
            <div>
                <a href="{$userContext}">&raquo; {ts}Back to Branner{/ts}</a>   
            </div>
        {/if}
    </div>
  {/if}
    </td>
    </tr>
    </table>
  {if ! ( $action & 1024 )}
    <table cellpadding=0 cellspacing=0 border=0 id="app-content">
        <tr>
           <td class="app-message" colspan=2>
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
