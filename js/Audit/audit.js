function selectActivity(i)
{
	// deselect current selection
	j = document.forms["Report"].currentSelection.value;
	ele = document.getElementById("civicase-audit-activity-" + j);
	ele.className = "activity";
	ele = document.getElementById("civicase-audit-header-" + j);
	ele.style.display = "none";
	ele = document.getElementById("civicase-audit-body-" + j);
	ele.style.display = "none";
	
	// select selected one
	ele = document.getElementById("civicase-audit-activity-" + i);
	ele.className = "activity selected";
	ele = document.getElementById("civicase-audit-header-" + i);
	ele.style.display = "block";
	ele = document.getElementById("civicase-audit-body-" + i);
	ele.style.display = "block";
	document.forms["Report"].currentSelection.value = i;
}