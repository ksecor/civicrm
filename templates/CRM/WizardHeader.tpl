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
        <li class="{$stepClass}">{$stepPrefix} {$smarty.section.step.iteration}. {$wizard.steps[step].title}</li>
    {/section}
   </ul>
</div>

<h2>{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}</h2>

