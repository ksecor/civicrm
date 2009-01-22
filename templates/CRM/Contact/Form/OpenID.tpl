{* This file provides the plugin for the openid block in the Location block *}
 
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}
{* @var $lid Contains the current location id in evaluation, and assigned in the Location.tpl file *}
{* @var $width Contains the width setting for the first column in the table *} 

     {* ----------- Primary OPENID BLOCK--------- *}
    <div class="form-item">
        <span class="labels">
            {$form.location.$index.openid.1.openid.label}
        </span>
        <span class="fields">
            <span>{$form.location.$index.openid.1.openid.html}</span>
             {if $allowed_to_login neq 1}
	            <span>{$form.location.$index.openid.1.allowed_to_login.html}</span>
             {/if}
             <br/>
             <span class="description font-italic">{ts}Full OpenID URL, ex: http://me.myopenid.com/{/ts}</span>
            {* Link to add a field. *}
            <span id="id_location_{$index}_openid_2_show" class="add-remove-link">
                {$form.location.$index.openid.2.show.html}
            </span>
        </span>
    </div>
    {* Spacer div contains floated elements *}
    <div class="spacer"></div>

    {section name = innerLoop start = 2 loop = $blockCount}
       {assign var=innerIndex value=$smarty.section.innerLoop.index}

        {* openid block {$innerIndex}. *}
        <div id="id_location_{$index}_openid_{$innerIndex}" class="form-item">
            <span class="labels">
             {$form.location.$index.openid.$innerIndex.openid.label}
            </span>
            <span class="fields">
              <span>{$form.location.$index.openid.$innerIndex.openid.html}</span>
              {if $hold neq 1}
        	      <span>{$form.location.$index.openid.$innerIndex.allowed_to_login.html}</span>
              {/if}
              {* Link to hide this field *}
              <span id="id_location_{$index}_openid_{$innerIndex}_hide" class="add-remove-link element-right">
              {$form.location.$index.openid.$innerIndex.hide.html}
              </span>
              {* Link to add another field.*}
              {if $innerIndex LT $blockCount}
                {assign var=j value=$innerIndex+1}
                <span id="id_location_{$index}_openid_{$j}_show" class="add-remove-link">
                    {$form.location.$index.openid.$j.show.html}
                </span>
              {/if}
            </span>
            
            {* Spacer div contains floated elements *}
            <div class="spacer"></div>
        </div>
	{/section}
