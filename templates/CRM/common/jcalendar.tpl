{assign var="timeElement" value=$elementName|cat:'_time'}
{$form.$elementName.html|crmReplace:class:twelve}&nbsp;&nbsp;{$form.$timeElement.label}&nbsp;&nbsp;{$form.$timeElement.html|crmReplace:class:six}
{literal}
<script type="text/javascript">

 var trig_date   = '#'+{/literal}"{$elementName}"{literal};
 var trig_time   = '#'+{/literal}"{$elementName}"{literal}+'_time';
 var cal_img     = {/literal}"{$config->resourceBase}i/cal.gif"{literal};
// var time_img    = {/literal}"{$config->resourceBase}packages/jquery/css/images/calendar/spinnerDefault.png"{literal};
 var curDateTime = new Date();
 var currentYear = curDateTime.getFullYear();
 var date_format = time_fomat  = null;
 var doTime      = {/literal}"{$doTime}"{literal};
 var ampm        = ({/literal}{$ampm}{literal}) ? false : true;
{/literal}

{if !$doTime}
    {literal} date_format = {/literal}"{$config->dateformatMonthVar}"{literal}+" d yy ";{/literal}
{elseif $doTime}
    {literal} date_format = {/literal}"{$config->datetimeformatMonthVar}"{literal}+" d yy";{/literal}
       {if $ampm}
            {literal} date_format = date_format; time_format = "{/literal}{$config->datetimeformatHourVar}"{literal}+" i A";{/literal}
       {else}
            {literal} date_format = date_format; time_format = "{/literal}{$config->datetimeformatHourVar}"{literal}+" i H";{/literal}
       {/if}
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

cj(trig_date).datepicker({ showOn            : 'both',
			               closeAtTop        : true, 
 			               buttonImage       : cal_img, 
                           buttonImageOnly   : true, 
                           dateFormat        : date_format,
                           changeMonth       : true, 
                           changeYear        : true,
                           yearRange         : '-'+startYear+':+'+endYear
              });

if(doTime) {
  cj(trig_time).timeEntry({ show24Hours : ampm });
}

</script>

{/literal}
