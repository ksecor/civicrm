{if !$printOnly} {* NO print section starts *}
    {if $criteriaForm} {* criteria section starts *}
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
                {include file="CRM/Report/Form/Criteria.tpl"}
            </fieldset>
        </div> {* search div section ends *}
    {/if} {* criteria section ends *}

    {if $instanceForm and $rows} {* settings section starts *}
        <div id="id_{$instanceForm}_show" class="section-hidden section-hidden-border">
            <a href="#" onclick="hide('id_{$instanceForm}_show'); show('id_{$instanceForm}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a>
            <label>{if $mode eq 'template'}{ts}Create Report{/ts}{else}{ts}Update Report{/ts}{/if}</label>
            <br />
        </div>

        <div id="id_{$instanceForm}">
            <fieldset>
                <legend>
                    <a href="#" onclick="hide('id_{$instanceForm}'); show('id_{$instanceForm}_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{if $mode eq 'template'}{ts}Create Report{/ts}{else}{ts}Update Report{/ts}{/if}
                </legend>
                <div id="instanceForm">
                    {include file="CRM/Report/Form/Instance.tpl"}
                    {assign var=save value="_qf_"|cat:$form.formName|cat:"_submit_save"}
                    <div>
                        <br />
                        {$form.$save.html}
                    </div> 
                </div>
            </fieldset>
        </div>
    {/if} {* settings section ends *}


    {* build the print pdf buttons *}
    {if $rows}
        {assign var=print value="_qf_"|cat:$form.formName|cat:"_submit_print"}
        {assign var=pdf   value="_qf_"|cat:$form.formName|cat:"_submit_pdf"}
        {assign var=csv   value="_qf_"|cat:$form.formName|cat:"_submit_csv"}
        {assign var=group value="_qf_"|cat:$form.formName|cat:"_submit_group"}
        {assign var=chart value="_qf_"|cat:$form.formName|cat:"_submit_chart"}
        <table style="border:0;">
            <tr>
                <td>
                    <table class="form-layout-compressed">
                        <td>{$form.$print.html}&nbsp;&nbsp;</td>
                        <td>{$form.$pdf.html}&nbsp;&nbsp;</td>
                        <td>{$form.$csv.html}&nbsp;&nbsp;</td>
                        <td>{$form.groups.html}&nbsp;{$form.$group.html}</td>
                        {if $instanceUrl}
                            <td>&nbsp;&nbsp;&raquo;&nbsp;<a href="{$instanceUrl}">{ts}Available Report(s) For This Template{/ts}</a></td>
                        {/if}
                    </table>
                </td>
                {if $chartSupported}
                    <td>
                        <table class="form-layout-compressed" align="right">
                            <td align="right">{$form.charts.html}&nbsp;&nbsp;{$form.$chart.html}</td>                        
                        </table>
                    </td>
                {/if}
            </tr>
        </table>
    {/if}

    <script type="text/javascript">
        var showBlocks = [];
        var hideBlocks = [];
        {if $criteriaForm}
            {if $rows}
               showBlocks[0] = "id_{$formTpl}_show";
               hideBlocks[0] = "id_{$formTpl}";
            {else}
               hideBlocks[0] = "id_{$formTpl}_show";
               showBlocks[0] = "id_{$formTpl}";
            {/if}
        {/if}

        {if $instanceForm and $rows}
            hideBlocks[1] = "id_{$instanceForm}";
            showBlocks[1] = "id_{$instanceForm}_show";
        {/if}

        {* hide and display the appropriate blocks as directed by the php code *}
        on_load_init_blocks( showBlocks, hideBlocks );
    </script>

{/if} {* NO print section ends *}