<script type="text/javascript" src="{$config->resourceBase}js/Common.js"></script>
{if ! empty( $fields )}
{* wrap in crm-container div so crm styles are used *}
<div id="crm-container" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">

{if $mode eq 8 || $mode eq 1}
{include file="CRM/Form/body.tpl"}
{/if}
    {strip}
    {if $help_pre && $action neq 4}
    <div class="messages help">{$help_pre}
    </div>
    {/if}
    {assign var=zeroField value="Initial Non Existent Fieldset"}
    {assign var=fieldset  value=$zeroField}
    {foreach from=$fields item=field key=name}

    {if $field.groupTitle != $fieldset}
        {if $fieldset != $zeroField}
            {if $addCAPTCHA }
              <tr>
               <td></td>
               <td>{$form.captcha_image.html}</td>
             </tr>
             <tr> 
               <td></td>   
               <td>{$form.captcha_phrase.html}
                 <div class="messages help">{$form.captcha_phrase.label}</div>
                </td>
             </tr>
           {/if}   
           </table>
           {if $groupHelpPost}
              <div class="messages help">{$groupHelpPost}</div>
           {/if}

           {if $mode ne 8}
              </fieldset>
              </div>
           {/if}
        {/if}


        {if $mode ne 8} 
           {assign var="groupId" value="id_"|cat:$field.group_id}
           <div id="{$groupId}_show" class="data-group">
              <a href="#" onclick="hide('{$groupId}_show'); show('{$groupId}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}{$field.groupTitle}{/ts}</label><br />
           </div>

           <div id="{$groupId}">
            <fieldset><legend><a href="#" onclick="hide('{$groupId}'); show('{$groupId}_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}{$field.groupTitle}{/ts}</legend>
        {/if}
        {assign var=fieldset  value=`$field.groupTitle`}
        {assign var=groupHelpPost  value=`$field.groupHelpPost`}
        {if $field.groupHelpPre}
            <div class="messages help">{$field.groupHelpPre}</div>
        {/if}
        <table class="form-layout-compressed">
     {/if}

    {assign var=n value=$field.name}
    {if $field.options_per_line}
	<tr>
        <td class="option-label">{$form.$n.label}</td>
        <td>
	    {assign var="count" value="1"}
        {strip}
        <table class="form-layout-compressed">
        <tr>
          {* sort by fails for option per line. Added a variable to iterate through the element array*}
          {assign var="index" value="1"}
          {foreach name=outer key=key item=item from=$form.$n}
          {if $index < 10}
              {assign var="index" value=`$index+1`}
          {else}
              <td class="labels font-light">{$form.$n.$key.html}</td>
              {if $count == $field.options_per_line}
                  </tr>
                   <tr>
                   {assign var="count" value="1"}
              {else}
          	       {assign var="count" value=`$count+1`}
              {/if}
          {/if}
          {/foreach}
        </tr>
        </table>
        {/strip}
        </td>
    </tr>
	{else}
        <tr><td class="label">{$form.$n.label}</td><td>{$form.$n.html}</td></tr>
	  {if $form.$n.type eq 'file'}
	      <tr><td class="label"></td><td>{$customFiles.$n}</td></tr>
	  {/if} 
	{/if}
        {* Show explanatory text for field if not in 'view' mode *}
        {if $field.help_post && $action neq 4}<tr><td>&nbsp;</td><td class="description">{$field.help_post}</td></tr>
        {/if}

    {/foreach}
        {if $addToGroupId}
	        <tr><td class="label">{$form.group[$addToGroupId].label}</td><td>{$form.group[$addToGroupId].html}</td></tr>
	    {/if}
       
        {if $mode eq 8 || $mode eq 4 || $mode eq 1}
            {if $addCAPTCHA }
              <tr>
               <td></td>
               <td>{$form.captcha_image.html}</td>
             </tr>
             <tr> 
               <td></td>   
               <td>{$form.captcha_phrase.html}
                 <div class="messages help">{ts}Please enter the phrase as displayed in the image{/ts}</div>
                </td>
             </tr>
           {/if}   
        {/if}
    </table>
{if $field.groupHelpPost}
    <div class="messages help">{$field.groupHelpPost}</div>
{/if}
    {if $mode ne 8}
    </fieldset>
    </div>
    {/if}
{if $mode eq 4}
<div class="crm-submit-buttons"> 
     {$form.buttons.html}
</div>
{/if}
     {if $help_post && $action neq 4}<br /><div class="messages help">{$help_post}</div>{/if}
    {/strip}

</div> {* end crm-container div *}
{/if} {* fields array is not empty *}

<script type="text/javascript">
  {if $mode ne 8}

    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

    {* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
    
  {/if}

  {literal}
  function popUp (path) 
  {
     window.open(path,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,screenX=150,screenY=150,top=150,left=150')
  }
  {/literal}	
</script>