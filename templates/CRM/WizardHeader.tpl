{if count( $wizard.steps ) > 1}
{* wizard.style variable is passed by some Wizards to allow alternate styling for progress "bar". *}
<div id="wizard-steps">
   <ul class="wizard-bar{if $wizardStyle.barClass}-{$wizardStyle.barClass}{/if}">
    {section name=step loop=$wizard.steps}
        {if $wizard.currentStepNumber > $smarty.section.step.iteration}
            {assign var="stepClass" value="past-step"}
            {assign var="stepPrefix" value=$wizardStyle.stepPrefixPast}
        {elseif $wizard.currentStepNumber == $smarty.section.step.iteration}
            {assign var="stepClass" value="current-step"}
            {assign var="stepPrefix" value=$wizardStyle.stepPrefixCurrent}
        {else}
            {assign var="stepClass" value="future-step"}
            {assign var="stepPrefix" value=$wizardStyle.stepPrefixFuture}
        {/if} 
        <li class="{$stepClass}">{$stepPrefix} {$smarty.section.step.iteration}. {$wizard.steps[step].title}</li>
    {/section}
   </ul>
</div>
{if $wizardStyle.showTitle}
    <h2>{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}</h2>
{/if}
{/if}

