{if ! $trigger}
  {assign var=trigger value=trigger}
{/if}

{literal}
<script type="text/javascript">
    var obj = new Date();
    var currentYear = obj.getFullYear();
{/literal}
{if $offset}
    var startYear = currentYear - {$offset};
    var endYear   = currentYear + {$offset};
{else}
    var startYear = {$startDate};
    var endYear   = {$endDate};
{/if}

{literal}
    Calendar.setup(
      {
{/literal}
{if !$doTime}
         dateField   : "{$dateVar}[d]",
         monthField  : "{$dateVar}[{$config->dateformatMonthVar}]",
         yearField   : "{$dateVar}[Y]",
{elseif $doTime}
         dateField   : "{$dateVar}[d]",
         monthField  : "{$dateVar}[{$config->datetimeformatMonthVar}]",
         yearField   : "{$dateVar}[Y]",
       {if $ampm}
         hourField   : "{$dateVar}[{$config->datetimeformatHourVar}]",
         minuteField : "{$dateVar}[i]",
         ampmField   : "{$dateVar}[A]",       
         showsTime   : true,
         timeFormat  : 12,
        {else}
         hourField   : "{$dateVar}[H]",
         minuteField : "{$dateVar}[i]",
         ampmField   : false,
         showsTime   : true,
         timeFormat  : 24,
        {/if}
{/if}
         range       : [startYear, endYear],
         button      : "{$trigger}"
{literal}
      }
    );
</script>
{/literal}


