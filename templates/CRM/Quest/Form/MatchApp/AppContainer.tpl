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
                {if $questURL} 
                    <div>
                        <a href="{$questURL}">&raquo; {ts}Back to Branner{/ts}</a>   
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

            {if $appName EQ 'Teacher' || $appName EQ 'Counselor'}
                {* Counselor and Teacher forms don't have sections. *}
                {include file="CRM/common/WizardHeader.tpl}
                <br />
                <div class="newsblock">
                    <table cellpadding=0 cellspacing=0 border=0>
                      <tr>
                        <td class="header">Recommendation Information</td>
                      </tr>
                      <tr>
                        <td class="newstext">Thank you for filling out<br />this recommendation for: <br />                      <br>
                            <strong>{$student_welcome_name}</strong>
                        </td>
                      </tr>
                    </table>
                </div>
            {else}
                {include file="CRM/common/SectionNav.tpl"}
            {/if}
            
            <br />
            <div class="help-box">
              <strong>Need Help?</strong><br />
              Email us with your questions
            </div>
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
            <div class="help-box">
              <strong> Announcements</strong><br />
              Remember that all<br />applications must be<br />completed by<br />October 1st, 2006. <br /><br />
              All recommendations<br />must be completed by<br />October 8th, 2006.<br /><br />
              Please be sure to use the<br />"Submit Application"<br />button to make sure that<br />you completed all<br />required fields.
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
        {if $questURL} 
            <div>
                <a href="{$questURL}">&raquo; {ts}Back to Branner{/ts}</a>   
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
