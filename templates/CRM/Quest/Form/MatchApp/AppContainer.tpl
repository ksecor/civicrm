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
                Note: The application deadline is Oct 1, 2006. ( * = required field)<br />You must click 'Save &amp; Continue' to save your changes.
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
            {include file="CRM/common/SectionNav.tpl}
            <br />
            <div class="help-box">
              <strong>Need Help?</strong><br />
              <a href="http://www.questbridge.org/recruitment/support_student_view.html" TARGET="_blank">Chat with a Quest staff member.</a><br />
            </div>
<!--
            <div class="help-box">
              <strong>Need Help?</strong><br />
              &nbsp;&nbsp;<a href="javascript:chatW=window.open('http://www.questbridge.org/support/live_support.html','Support','width=600,height=470,resizable=yes'); chatW.focus()">Talk to a Quest team<br />&nbsp;&nbsp;member</A><br />
            </div>
//-->
            <div id="application-status">
              <strong>Application Status</strong><br />
              &nbsp;&nbsp;{$taskStatus}
            </div>

            <div class="help-box">
              <strong>Navigation Color Guide</strong><br />
              <B>Green</B>: Section not<br />completed or section<br />has error(s)<br />
              <B>Blue</B>: Section has been<br />completed or is available<br />for editing<br />
              <B>Gray</B>: Section is not<br />available for editing<br />
            </div>
<!--
            <div class="help-box">
                <table cellpadding=0 cellspacing=0 border=0>
                  <tr>
                    <td bgcolor="#396872" class="header"><strong> Announcements</strong></td>
                  </tr>
                  <tr>
                    <td class="newstext"> Remember that all applications must be completed by October 1st, 2006. <br /><br />
                      All recommendations must be completed by October 8th, 2006.<br /><br />
                    Please be sure to use the "Submit Application" button to make sure that you completed all required fields.
                    </td>
                  </tr>
                </table>
            </div>
//-->
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
