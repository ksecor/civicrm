
<div id="intro_text">{$intro}

 <div id="form" class="form-item">   
      
    {*<dt>{$form.intro.label}</dt> *} 
    <dt>{$form.first_name_user.label}</dt><dd>{$form.first_name_user.html}</dd> 
    <dt>{$form.last_name_user.label}</dt><dd>{$form.last_name_user.html}</dd>   
    <dt>{$form.email_user.label}</dt><dd>{$form.email_user.html}</dd>     
    <dt>{$form.suggested_message.label}</dt><dd>{$form.suggested_message.html}</dd> 
 
    {section name=loop start=1 loop=4}
        {assign var=idx value=$smarty.section.loop.index}
        <dt>{$form.friend.$idx.first_name.label}</dt><dd>{$form.friend.$idx.first_name.html}</dd>
	<dt>{$form.friend.$idx.last_name.label}</dt><dd>{$form.friend.$idx.last_name.html}</dd>
	<dt>{$form.friend.$idx.email.label}</dt><dd>{$form.friend.$idx.email.html}</dd>
    {/section}      
    
    <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>     	    
<div>	
 </div>      
