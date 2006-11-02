{* Quest College Match Application: Display container for application pages. *}
{if $context EQ 'begin'}
  <div id="app-content">
  {if ! ( $action & 1024 ) }{* We skip greeting and nav buttons and .js for preview action, but include them for edit and view actions. *}
    <script type="text/javascript" src="{$config->resourceBase}js/Individual.js"></script>
    <table cellpadding="0" cellspacing="0" border="0" id="app-content">
        <tr>
            <td class="greeting">{$welcome_name}</td>
            <td class="app-message" align="center">
            {if $appName EQ 'Teacher' || $appName EQ 'Counselor'}
                {ts}Please note: the Recommendations deadline is Oct 8, 2006.  ( * = required field){/ts}
            {elseif $sectionName EQ 'Partner'}
                {ts}Please note: the Partner Supplements deadline is Oct 15, 2006.  ( * = required field){/ts}
            {else}
                {ts}Note: The Application deadline is Oct 1, 2006. ( * = required field)<br />You must click 'Save &amp; Continue' to save your changes.{/ts}
            {/if}
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
    <table cellpadding="0" cellspacing="0" border="0" id="app-content">
        <tr>
          {if ! ( $action & 1024 )}
            <td valign="top" nowrap id="app-left-nav">

            {if $appName EQ 'Teacher' || $appName EQ 'Counselor' || $sectionName EQ 'Partner'}
                {* Counselor, Teacher and Partner Supplement apps don't have sections. *}
                {include file="CRM/common/WizardHeader.tpl}
                <br />
            {/if}
            {if $appName EQ 'Teacher' || $appName EQ 'Counselor'}
                <div class="help-box">
                    <strong>Recommendation Information</strong><br />
                    Thank you for filling out<br />this recommendation for:<br /><br />
                    <strong>{$student_welcome_name}</strong>
                </div>
            {/if}
            {if $sectionName NEQ 'Recommendation'} {* Recommendation is the standalone fix-recommender form. *}
                {if $appName EQ 'MatchApp' && $sectionName NEQ 'Partner'}
                    {include file="CRM/common/SectionNav.tpl"}
                {/if}
                {edit}
                    <br /><br />
                    <ul class="section-list">
                      <li class="current-section">
                        <div align="center"><a href="#" onclick="saveDraft(); return false;">Save Draft</a></div>
                      </li>
                      
                    {if $appName EQ 'MatchApp' && $sectionName NEQ 'Partner'}
                      {* Submit is a category step for MatchApp and a wizard step for Recommendations and Partner Supplement apps *} 
                      <li class="current-section">
                        <div align="center"><strong>{if $category.steps.Submit.link}<a href="{$category.steps.Submit.link}">{/if}Submit Application{if $category.steps.Submit.link}</a>{/if}</strong></div>
                      </li>
                    {else}
                        {assign var="submitStep" value=$wizard.count}
                      <li class="current-section">
                        <div align="center"><strong>{if $wizard.steps.$submitStep.link}<a href="{$wizard.steps.$submitStep.link}">{/if}{$wizard.steps.$submitStep.title}{if $wizard.steps.$submitStep.link}</a>{/if}</strong></div>
                      </li>
                    {/if}
                    </ul>
                    <br />
                    </div>
                {/edit}
            {/if}
            
            <br />
            <div class="help-box">
              <strong>Need Help?</strong><br />
              <A HREF="http://www.questbridge.org/students/faqs.html" TARGET="_blank">Read more at our FAQs</A><br /><br />
              If you don't find an answer in<br />the FAQs, <a href="mailto:techsupport@questbridge.org">email us</a> with your<br />questions
            </div>

            <div id="application-status">
                {if $appName EQ 'Teacher' || $appName EQ 'Counselor'}
                  <strong>Recommendation Status</strong><br />
                  &nbsp;&nbsp;{$taskStatus}
                {else}
                  <strong>Application Status</strong><br />
                  &nbsp;&nbsp;{$appTaskStatus}<br /><br />
                  <strong>Current Section Status</strong><br />
                  &nbsp;&nbsp;{$taskStatus}
                {/if}
            </div>

            <div class="help-box">
              <strong>Navigation Color Guide</strong><br />
              <strong>Green</strong>: Section not<br />completed or section<br />has error(s)<br />
              <strong>Blue</strong>: Section has been<br />completed or is available<br />for editing<br />
              <strong>Gray</strong>: Section is not<br />available for editing<br />
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
        <td valign="top" class="rightside">
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
    <table cellpadding="0" cellspacing="0" border="0" id="app-content">
        <tr>
           <td class="app-message" colspan="2">
             {ts}You must click 'Save &amp; Continue' to save your changes.{/ts}
           </td>
    </table>
  {/if}
  </div>
  {edit}
    {* Include Javascript to hide and display the appropriate blocks as directed by the php code *}
    {include file="CRM/common/showHide.tpl"}
    <script type="text/javascript">
        var thisForm = document.forms["{$wizard.currentStepName}"];
        {literal}
        function saveDraft() {
            thisForm.is_save_draft.value = 1;
            thisForm.submit();
        }
        {/literal}
    </script>  
  {/edit}
{/if}
