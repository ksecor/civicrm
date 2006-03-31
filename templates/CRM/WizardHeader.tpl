{if count( $wizard.steps ) > 1}
{* wizard.style variable is passed by some Wizards to allow alternate styling for progress "bar". *}
<div id="wizard-steps">
   <ul class="wizard-bar{if $wizard.style.barClass}-{$wizard.style.barClass}{/if}">
    {section name=step loop=$wizard.steps}
        {assign var=i value=$smarty.section.step.iteration}
        {if $wizard.currentStepNumber > $i}
            {assign var="stepClass" value="past-step"}
            {if $wizard.steps[step].step}
                {assign var="stepPrefix" value=$wizard.style.stepPrefixPast|cat:$wizard.steps[step].stepNumber|cat:". "}
            {else}
                {assign var="stepPrefix" value=$wizard.style.subStepPrefixPast}
            {/if}
        {elseif $wizard.currentStepNumber == $smarty.section.step.iteration}
            {assign var="stepClass" value="current-step"}
            {if $wizard.steps[step].step}
                {assign var="stepPrefix" value=$wizard.style.stepPrefixCurrent|cat:$wizard.steps[step].stepNumber|cat:". "}
            {else}
                {assign var="stepPrefix" value=$wizard.style.subStepPrefixCurrent}
            {/if}
        {else}
            {assign var="stepClass" value="future-step"}
            {if $wizard.steps[step].step}
                {assign var="stepPrefix" value=$wizard.style.stepPrefixFuture|cat:$wizard.steps[step].stepNumber|cat:". "}
            {else}
                {assign var="stepPrefix" value=$wizard.style.subStepPrefixFuture}
            {/if}
        {/if} 
        {* wizard.steps[step].link value is passed for wizards/steps which allow clickable navigation *} 
        <li class="{$stepClass}">{$stepPrefix}{if $wizard.steps[step].link}<a href="{$wizard.steps[step].link}">{/if}{$wizard.steps[step].title}{if $wizard.steps[step].link}</a>{/if}</li>
    {/section}
   </ul>
</div>
{if $wizard.style.showTitle}
    <h2>{$wizard.currentStepTitle} {ts 1=$wizard.currentStepNumber 2=$wizard.stepCount}(step %1 of %2){/ts}</h2>
{/if}
{/if}

