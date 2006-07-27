{* Navigation template for multi-section Wizards *}
{if count( $category.steps ) > 0}
<div id="wizard-steps">
   <ul class="section-list">
    {foreach from=$category.steps item=step}
            {assign var=i value=$smarty.section.step.iteration}
            {if $step.current}
                {assign var="stepClass" value="current-step"}
            {else}
                {assign var="stepClass" value="future-step"}
            {/if}
            {if !$step.valid}
                {assign var="stepClass" value="$stepClass not-valid"}
            {/if}
            {* step.link value is passed for section usages which allow clickable navigation AND when section state is clickable *} 
            <li class="{$stepClass}">{if $step.link && $step.valid && !$step.current}<a href="{$step.link}">{/if}{$step.title}{if $step.link}</a>{/if}</li>
            {if $step.current}
                {include file="CRM/WizardHeader.tpl}
            {/if}
    {/foreach}
   </ul>
</div>
{/if}