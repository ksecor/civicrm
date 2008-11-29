{* Base template for case activities like - Open Case, Change Case Type/Status ..
   Note: 1. We will include all the activity fields here however each activity type file may build (via php) only those required by them. 
         2. Each activity type file can include its case fields in its own template, so that they will be included during activity edit.
*}

<fieldset><legend>{$activityType}</legend>
<table class="form-layout">
{if $clientName}
    <tr><td class="label font-size12pt">{ts}Client{/ts}</td><td class="font-size12pt bold view-value">{$clientName}</td></tr>
{/if}

{if $form.activity_subject.html}
    <tr><td class="label">{$form.activity_subject.label}</td><td>{$form.activity_subject.html}</td></tr>
{/if}
{if $form.medium_id.html and $form.activity_location.html}
    <tr>
        <td class="label">{$form.medium_id.label}</td>
        <td class="view-value">{$form.medium_id.html}&nbsp;&nbsp;&nbsp;{$form.activity_location.label} &nbsp;{$form.activity_location.html}</td>
    </tr> 
{/if}

{* injection *}
{if $activityTypeFile}
    {include file="CRM/Case/Form/Activity/$activityTypeFile.tpl"}
{/if}

{if $groupTree}
    <tr>
       <td colspan="2">{include file="CRM/Custom/Form/CustomData.tpl"}</td>
    </tr>
{/if}

{if $form.activity_details.html}
    <tr><td class="label">{$form.activity_details.label}</td><td class="view-value">{$form.activity_details.html|crmReplace:class:huge}</td>
    </tr>
{/if}

{if $form.duration.html}
    <tr>
      <td class="label">{$form.duration.label}</td>
      <td class="view-value">
        {$form.duration.html}
         <span class="description">{ts}Total time spent on this activity (in minutes).{/ts}
      </td>
    </tr> 
{/if}

    <tr>
        <td>&nbsp;</td><td class="buttons">{$form.buttons.html}</td>
    </tr>
</table>
</fieldset>
