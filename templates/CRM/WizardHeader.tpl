<div id="wizard-steps">
   <ul class="wizard-bar">
    {section name=step loop=$wizard.steps}
        {if $wizard.currentStepNumber > $smarty.section.step.iteration}
            {assign var="stepClass" value="past-step"}
            {assign var="stepPrefix" value="&radic;"}
        {elseif $wizard.currentStepNumber == $smarty.section.step.iteration}
            {assign var="stepClass" value="current-step"}
            {assign var="stepPrefix" value="&raquo;"}
        {else}
            {assign var="stepClass" value="future-step"}
            {assign var="stepPrefix" value=""}
        {/if} 
        <li class="{$stepClass}">{$stepPrefix} {$wizard.steps[step].title}</li>
    {/section}
   </ul>
</div>

<h2>{$wizard.currentStepTitle} (step {$wizard.currentStepNumber} of {$wizard.stepCount})</h2>

