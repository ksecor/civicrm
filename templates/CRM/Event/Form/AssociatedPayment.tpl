{* This template is used for showing the associated Contribuion related participant *}
<label>{ts}Associated Contribution{/ts}</label>
    	<div class="form-item">
         	{strip}
           	<table>
           		<tr class="columnheader">
                	    <th>{ts}Amount{/ts}</th>
                            <th>{ts}Type{/ts}</th>
                	    <th>{ts}Source{/ts}</th>
	                    <th>{ts}Received{/ts}</th>
                            <th>{ts}Thank-you Sent{/ts}</th>
			    <th>{ts}Status{/ts}</th>
                	    <th></th>
            		 </tr>
            	         <tr class="{cycle values="odd-row,even-row"} {$activeMember.class}">
                	    <td>{$contribution.total_amount}</td>
                	    <td>{$contribution.contributionType}</td>
                            <td>{$contribution.source}</td>
                            <td>{$contribution.receive_date|crmDate}</td>
                            <td>{$contribution.thankyou_date|crmDate}</td>
                            <td>{$contribution.contributionStatus}</td>
			    <td>{$contribution.action}</td>
            		    </tr>
                </table>
           	{/strip}
	</div>

