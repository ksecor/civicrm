{* this div is being used to apply special css *}
<div id="searchForm">
{if !$printOnly} {* NO print section starts *}
    <div id="id_{$formTpl}_show" class="section-hidden section-hidden-border">
        <a href="#" onclick="hide('id_{$formTpl}_show');show('id_{$formTpl}'); return false;">
          <img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a>
        <label>{ts}Report Criteria{/ts}</label>
        <br />
    </div>
    <div id="id_{$formTpl}"> {* search section starts *}
        <fieldset>
            <legend>
                <a href="#" onclick="hide('id_{$formTpl}'); show('id_{$formTpl}_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Report Criteria{/ts}
            </legend>

            <fieldset>
                <legend>{ts}Options{/ts}</legend>
                <table class="form-layout">
                    <tr>
                      <td>{$form.group_bys_country.html}&nbsp;{$form.group_bys_country.label}</td>
                    </tr>
                    <tr>
                        <td width="25%">{$form.is_repeat.html}&nbsp;{$form.is_repeat.label}</td>
                    </tr>
                </table>      
            </fieldset>

            <fieldset>
                <legend>{ts}Set Filters{/ts}</legend>
                <table class="form-layout">
                   <tr>
                     <td style="vertical-align: top;">{ts}Date Range One{/ts}</td>
                     <td colspan=2>{include file="CRM/Core/DateRange.tpl" fieldName='receive_date_r1}</td>
                  </tr>
                   <tr>
                     <td style="vertical-align: top;">{ts}Date Range Two{/ts}</td>
                     <td colspan=2>{include file="CRM/Core/DateRange.tpl" fieldName='receive_date_r2}</td>
                  </tr>
                </table>
            </fieldset>
 
            <div>{$form.buttons.html}</div>
        </fieldset>
    </div> {* search div section ends *}

    {if $instanceForm} {* settings section starts *}
    <div id="id_{$instanceForm}_show" class="section-hidden section-hidden-border">
        <a href="#" onclick="hide('id_{$instanceForm}_show'); show('id_{$instanceForm}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a>
        <label>{ts}Report Settings{/ts}</label>
        <br />
    </div>

    <div id="id_{$instanceForm}">
        <fieldset>
            <legend>
                <a href="#" onclick="hide('id_{$instanceForm}'); show('id_{$instanceForm}_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Report Settings{/ts}
            </legend>
            <div id="instanceForm">
                {include file="CRM/Report/Form/Instance.tpl"}
                {assign var=save value="_qf_"|cat:$form.formName|cat:"_submit_save"}
                <div>
                    <br />{$form.$save.html}
                </div> 
            </div>
        </fieldset>
    </div>

    {/if} {* settings section ends *}


    {* build the print pdf buttons *}
    {assign var=print value="_qf_"|cat:$form.formName|cat:"_submit_print"}
    {assign var=pdf   value="_qf_"|cat:$form.formName|cat:"_submit_pdf"}
    <div id="crm-submit-buttons">{$form.$print.html}&nbsp;&nbsp;{$form.$pdf.html}</div>

    <script type="text/javascript">
        var showBlocks = [];
        var hideBlocks = [];
        {if $rows}
            showBlocks[0] = "id_{$formTpl}_show";
            hideBlocks[0] = "id_{$formTpl}";
        {else}
            hideBlocks[0] = "id_{$formTpl}_show";
            showBlocks[0] = "id_{$formTpl}";
        {/if}

        {if $instanceForm and $rows}
            hideBlocks[1] = "id_{$instanceForm}";
            showBlocks[1] = "id_{$instanceForm}_show";
        {/if}

        {* hide and display the appropriate blocks as directed by the php code *}
        on_load_init_blocks( showBlocks, hideBlocks );
    </script>

{/if} {* NO print section ends *}

{if $form.charts.value.0 eq 'pieGraph' OR $form.charts.value.0 eq 'barGraph'}
    {include file="CRM/Report/Form/Layout/Graph.tpl"}
{else}
   {* search result listing *}
   {include file="CRM/Report/Form/Layout/Table.tpl"}
{/if}

</div>
