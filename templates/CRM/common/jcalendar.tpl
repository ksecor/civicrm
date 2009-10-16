{assign var="timeElement" value=$elementName|cat:'_time'}
{$form.$elementName.html|crmReplace:class:twelve}&nbsp;&nbsp;{$form.$timeElement.label}&nbsp;&nbsp;{$form.$timeElement.html|crmReplace:class:six}

<script type="text/javascript">
    {literal}
    var element_date   = '#'+{/literal}"{$elementName}"{literal};
    var element_time   = '#'+{/literal}"{$elementName}"{literal}+'_time';
    var cal_img     = {/literal}"{$config->resourceBase}i/cal.gif"{literal};
    var date_format = {/literal}"{$config->dateInputFormat}"{literal};
    // var time_img    = {/literal}"{$config->resourceBase}packages/jquery/css/images/calendar/spinnerDefault.png"{literal};
    var curDateTime = new Date();
    var currentYear = curDateTime.getFullYear();    
    {/literal}

var doTime  = false;
{if $timeElement}
    doTime  = true;
{/if}

{if $offset}
    {literal} 
          startYear = endYear = {/literal}{$offset}{literal};
    {/literal}

{else}
    {literal} 
          var startYear = currentYear - {/literal}{$startDate}{literal};
          var endYear   = ({/literal}{$endDate}{literal}) ? {/literal}{$endDate}{literal} - currentYear : 0 ;
    {/literal}
{/if}

    {literal}
    cj(element_date).datepicker({
                                    showOn            : 'both',
                                    closeAtTop        : true, 
                                    buttonImage       : cal_img, 
                                    buttonImageOnly   : true, 
                                    dateFormat        : date_format,
                                    changeMonth       : true,
                                    changeYear        : true,
                                    yearRange         : '-'+startYear+':+'+endYear
                                });

    if ( doTime ) {
        var time_format = {/literal}{$config->timeInputFormat}{literal};
        cj(element_time).timeEntry({ show24Hours : time_format });
    }

    {/literal}
</script>


