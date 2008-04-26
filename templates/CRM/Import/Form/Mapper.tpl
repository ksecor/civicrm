<fieldset>
<legend>Mapper</legend>

<div class="form-item">
   <dl>
     {section name=count start=1 loop=`$maxMapper`}
     {assign var='i' value=$smarty.section.count.index}
       <dt>{$form.mapper[$i].label}</dt><dd>{$form.mapper[$i].html}{$hsExtra[$i]}</dd>

       {literal}
        <script type="text/javascript">
            var selId = "id_map_" + {/literal}"mapper[{$i}]"{literal} + "_1";
            document.getElementById(selId).style.display = "none";
        </script>
       {/literal}

     {/section}
   </dl>    
</div>
    
<div id="crm-submit-buttons" class="form-item">
<dl>
   <dt>&nbsp;</dt><dd>{$form.buttons.html}</dd>
</dl>
</div>

</fieldset>

{literal}
<script type="text/javascript">
      function showHideNextSelector( sel1Name, sel1Val ) {
            var sel1Id = "id_map_" + sel1Name + "_1";
            if (sel1Val) {
                document.getElementById(sel1Id).style.display = "inline";
            } else {
                document.getElementById(sel1Id).style.display = "none";
            }
      }
</script>
{/literal}
