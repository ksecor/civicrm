{* Quest Pre-application:  College Match Ranking Information section *}
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td colspan="2" class="grouplabel">The College Match   program is a 'binding' decision. If you match with any of the school(s) you list, you will attend that school. <br>
                      <br>
                      The College Match program uses a ranking system for matching students with our partner colleges. Our process consists of two rankings, a preliminary ranking and a final ranking. The preliminary ranking is not binding, the final ranking is binding. With this application you will be asked to enter your preliminary ranking. Two weeks after the application deadline, you will be asked for your final ranking. <br>
                      <br>
                      You can learn more about the <u><a href="http://college_match.tpl" target="_blank">College Match Process</u></a>
                      <br>
                      <br>
                      Please enter your preliminary ranking of colleges and universities you are interested in attending. If you are not interested in a college, select 'Not Interested' as the ranking. We recommend you research the colleges before you select a ranking for them. You can learn more about the colleges and universities by clicking on the link next to each college name.</td>
                  </tr>
                  <tr>
                    <td class="grouplabel"><strong>Colleges</strong></td>
                    <td class = "nowrap"><strong>Ranking</strong></td>
                  </tr>
      {section name=rowLoop start=1 loop=14}
       {assign var=i value=$smarty.section.rowLoop.index}
      {assign var=collegeTypes value=$collegeType}
      {assign var=collegeRank value="college_ranking_"|cat:$i}   
    
     <tr>

  	  <td class="grouplabel">{$collegeTypes[$i]}&nbsp;&nbsp;<a href="http://">(learn more)</a></td>
      <td class="nowrap">{$form.$collegeRank.html}</td>
    </tr>
{/section}
 
</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}
<div class="newsblock">
            <table border="1" cellpadding="0" cellspacing="0" width="900">
            <tbody><tr>
                <td><span class="style1"><strong> Engineer's Note </strong></span></td>
              </tr>
              <tr>
                <td class="newstext"><span class="style1"> 29. No two colleges can have the same ranking, between 1 - 13. <br>
                  30. Any college (except Rice, Oberlin, Trinity, Scripps) that is ranked, should have its supplemental information displayed in the 'Partner Supplement' section. (Rice doesn't require the supplement to be completed for Early Decision)  (Oberlin, Trinity, Scripps do not have a supplement).</span></td>

              </tr>
            </tbody></table>
