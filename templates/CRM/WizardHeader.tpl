<div id="wizard-steps">
   <ul class="wizard-bar">
    {section name=step loop=$wizard.steps}
        {if $wizard.currentStepNumber > $smarty.section.step.iteration}
            {assign var="stepClass" value="past-step"}
        {elseif $wizard.currentStepNumber == $smarty.section.step.iteration}
            {assign var="stepClass" value="current-step"}
        {else}
            {assign var="stepClass" value="future-step"}
        {/if} 
        <li class="{$stepClass}">{$wizard.steps[step].title}</li>
    {/section}
   </ul>
</div>

<h2>{$wizard.currentStepTitle} (step {$wizard.currentStepNumber} of {$wizard.stepCount})</h2>

