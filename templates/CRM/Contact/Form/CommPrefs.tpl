{* This file provides the plugin for the communication preferences in all the three types of contact *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

<div id="commPrefs">
<fieldset><legend>{ts}Communication Preferences{/ts}</legend>
	<table class="form-layout-compressed">
    <tr>
        <td>{$form.privacy.label}</td>
        <td>{$form.privacy.html}</td>
    </tr>
    <tr>
        <td>{$form.preferred_communication_method.label}</td>
        <td>
           {assign var="count" value="1"}
           {strip}
             <table class="form-layout">
              <tr>
               {assign var="index" value="1"}
               {foreach name=outer key=key item=item from=$form.preferred_communication_method}
                  {if $index < 10}
                    {assign var="index" value=`$index+1`}
                  {else}
                   <td class="labels font-light">{$form.preferred_communication_method.$key.html}</td>
                    {if $count == 5}
                    
                    {assign var="count" value="1"}
                    {else}
                    {assign var="count" value=`$count+1`}
                    {/if}
                   {/if}
              {/foreach}
              {*{$form.preferred_communication_method[1].html}*}
              </tr>
            </table>
           {/strip}
            <div class="description font-italic">
                {ts}Select the preferred method of communicating with this contact.{/ts}
            </div>
        </td>
    </tr>
    <tr>
        <td>{$form.preferred_mail_format.label}</td>
        <td>
            {$form.preferred_mail_format.html} {help id="id-emailFormat"}
        </td>
    </tr>
    <tr>
        <td>{$form.is_opt_out.label}</td>
        <td>
            {$form.is_opt_out.html} {help id="id-optOut"}
        </td>
    </tr>
    </table>
</fieldset>
</div>
