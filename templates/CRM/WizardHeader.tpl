<h3>{$wizard.currentStepTitle}</h3>

<div id="wizard-steps">
   <ul id="wizard-list">
   {foreach from=$wizard.steps item=step}
    <li>{$step.title}</li>
   {/foreach}
   </ul>
</div>