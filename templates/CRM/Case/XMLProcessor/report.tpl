<?xml version="1.0" encoding="UTF-8"?>
<Case>
  <Client>{$case.clientName}</Client>
  <CaseType>{$case.caseType}</CaseType>
  <CaseSubject>{$case.subject}</CaseSubject>
  <CaseStatus>{$case.status}</CaseStatus>
  <CaseOpen>{$case.start_date}</CaseOpen>
  <CaseClose>{$case.end_date}</CaseClose>
  <ActivitySet>
    <Label>{$activitySet.label}</Label>
    <IncludeActivities>All</IncludeActivities>
    <Redact>false</Redact>
{foreach from=$activities item=activity}
    <Activity>
       <EditURL>{$activity.editURL}</EditURL>
       <Fields>
       {foreach from=$activity.fields item=field}
          <Field>
            <Label>{$field.label}</Label>
            <Value>{$field.value}</Value>
            <Type>{$field.type}</Type>
          </Field>
       {/foreach}
       {if $activity.customGroups}
         <CustomGroups>
         {foreach from=$activity.customGroups item=customGroup}
            <CustomGroup>
               <GroupName>{$customGroup.name}</GroupName>
               {foreach from=$customGroup.fields item=field}
                  <Field>
                    <Label>{$field.label}</Label>
                    <Value>{$field.value}</Value>
                    <Type>{$field.type}</Type>
                  </Field>
               {/foreach}
            </CustomGroup>
         {/foreach}
         </CustomGroups>
       {/if}
       </Fields>
    </Activity>
{/foreach}
  </ActivitySet>
</Case>

