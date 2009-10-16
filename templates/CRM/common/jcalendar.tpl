{assign var="timeElement" value=$elementName|cat:'_time'}
{$form.$elementName.html|crmReplace:class:twelve}&nbsp;&nbsp;{$form.$timeElement.label}&nbsp;&nbsp;{$form.$timeElement.html|crmReplace:class:six}

<script type="text/javascript">
    {literal}
    var element_date   = '#'+{/literal}"{$elementName}"{literal};
    var element_time   = '#'+{/literal}"{$elementName}"{literal}+'_time';
    var cal_img        = {/literal}"{$config->resourceBase}i/cal.gif"{literal};
    var date_format    = {/literal}"{$config->dateInputFormat}"{literal};
    if ( cj( element_date).attr('formatType') ) {
        date_format =  cj( element_date).attr('formatType');
    }
    
    // var time_img    = {/literal}"{$config->resourceBase}packages/jquery/css/images/calendar/spinnerDefault.png"{literal};
    {/literal}

    {if $timeElement}
        var time_format = {$config->timeInputFormat};
        {literal}
            cj(element_time).timeEntry({ show24Hours : time_format });
        {/literal}
    {/if}

    var startYear = cj( element_date ).attr('startOffset');
    var endYear   = cj( element_date ).attr('endOffset');
    
    {literal} 
    cj(element_date).datepicker({
                                    showOn            : 'both',
                                    closeAtTop        : true, 
                                    buttonImage       : cal_img, 
                                    buttonImageOnly   : true, 
                                    dateFormat        : date_format,
                                    changeMonth       : true,
                                    changeYear        : true,
                                    yearRange         : '-'+startYear+':+'+endYear,
                                    showButtonPanel   : true
                                });
    {/literal}
</script>


