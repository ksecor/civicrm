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
         dateField   : "{$dateVar}[d]",
         monthField  : "{$dateVar}[M]",
         yearField   : "{$dateVar}[Y]",
{if $doTime}
         hourField   : "{$dateVar}[h]",
         minuteField : "{$dateVar}[i]",
         ampmField   : "{$dateVar}[A]",
         showsTime   : true,
         timeFormat  : "12",
{/if}
         range       : [startYear, endYear],
         button      : "{$trigger}"
{literal}
      }
    );
</script>
{/literal}


