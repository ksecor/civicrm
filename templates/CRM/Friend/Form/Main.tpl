{include file="CRM/common/WizardHeader.tpl"}
{capture assign=docURLTitle}{ts}Opens online documentation in a new window.{/ts}{/capture}





{*<title><h2>{$title}</title>*}
{*<div id="intro_text">*}

 <div id="form" class="form-item">   
      
    {*<dt>{$form.intro.label}</dt> *} 
    <dt>{$form.first_name.label}</dt><dd>{$form.first_name.html}</dd> 
    <dt>{$form.last_name.label}</dt><dd>{$form.last_name.html}</dd>   
    <dt>{$form.email.label}</dt><dd>{$form.email.html}</dd>     
    <dt>{$form.suggested_message.label}</dt><dd>{$form.suggested_message.html}</dd> 
 
    {section name=loop start=1 loop=4}
        {assign var=idx value=$smarty.section.loop.index}
        <dt>{$form.first_name.$idx.label}</dt><dd>{$form.first_name.$idx.html}</dd>
	<dt>{$form.last_name.$idx.label}</dt><dd>{$form.last_name.$idx.html}</dd>
	<dt>{$form.email.$idx.label}</dt><dd>{$form.email.$idx.html}</dd>
    {/section}      
    
    <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>     	    
	
 </div>      
