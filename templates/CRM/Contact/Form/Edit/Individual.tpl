{* tpl for building Individual related fields *}
<table class="form-layout-compressed">
        <tr class="last-row">
            <td>
                <label for="prefix_id">Prefix</label><br />
                <select name="prefix_id" id="prefix_id" class="form-select">
                    <option value="">
                    </option>
                    <option value="1"
               selected="selected">Mrs</option>
                    <option value="2">Ms</option>

                    <option value="3">Mr</option>
                    <option value="4">Dr</option>
                </select>
            </td>
            <td>
                <label for="first_name">First Name</label><br />
                <input maxlength="64"
                value="Jane"
                size="18"  name="first_name" type="text" id="first_name" />
            </td>
            <td>
                <label for="middle_name">Middle Name</label><br />
                <input maxlength="64"
                size="15" name="middle_name" type="text" id="middle_name" />
            </td>
            <td>
                <label for="last_name">Last Name</label><br />
                <input maxlength="64"
                value="Doe"
                size="18" name="last_name" type="text" id="last_name" />
            </td>
            <td>                                                            <label
            for="suffix_id">Suffix</label><br />
                <select name="suffix_id" id="suffix_id" class="form-select">
                    <option value="">
               </option>

                    <option value="1">Jr</option>
                    <option value="2">Sr</option>
                    <option value="3">II</option>
                    <option value="4">III</option>
                    <option value="5">IV</option>
                    <option value="6">V</option>

                    <option value="7">VI</option>
                    <option value="8">VII</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2"><label for="current_employer">Current Employer</label><br />
            <div align="right">
                <input size="24" name="current_employer" type="text" id="current_employer" value="SomeFoundation" />
            </div>
            </td>
                
            <td><label for="job_title">Job title</label><br />
                <input maxlength="64" size="15" name="job_title" type="text" id="job_title"
            value="public relations"/>
            </td>
            <td colspan="2"><label for="nick_name">Nick Name</label><br />
                    <input maxlength="128" size="18" name="nick_name" type="text" id="nick_name" />
			
			
			</td>
        </tr>
    </table>