# This is a test case of using Selenium and its Ruby bindings

#class for conversion method
class CPSConvert
  #function for Date Conversion 
  def convertDate(dateToConvert)
    strDate = String.new(dateToConvert)
    dateArray = strDate.split('-')
    
    #convert month to format(mon)
    strDate1 = Time.new()
    dateArray[1] = strDate1.strftime('%b')
    return(dateArray)
  end
  
  #function for String concatenation 
  def convertString(stringToAdd)
    strConvert  = String.new(stringToAdd) 
    string1 = "label="
    string2 = string1.concat(stringToAdd)
    return string2
  end
  #function to check wether string is empty
  def checkString(string)
    str = StringScanner.new(string)
    if str.scan(/\s+/) then
      #if string is empty
      return true
    else
      return false
    end
  end
end

#class to add Personal Information
class CPSPersonalInformation
  def personalDetails(selenium, studentInfo, doc)
    convertObj = CPSConvert.new()
    
    selenium.type   "document.Personal.first_name",studentInfo[6].text
    selenium.type   "middle_name", studentInfo[7].text
    selenium.type   "document.Personal.last_name", studentInfo[8].text

    if studentInfo[5].text then
      suffix = convertObj.convertString(studentInfo[5].text)
      selenium.select "suffix_id", suffix   
    else
      selenium.select "suffix_id", "label=- suffix -"
    end
    
    selenium.type   "nick_name",studentInfo[3].text  

    # selenium.check "document.Personal.gender_id[1]"
    if studentInfo[9].text == "Female" then
      selenium.check "gender_id"
    else
      selenium.check "document.Personal.gender_id[1]"
    end
    
    selenium.type "uploadFile", ""

    #convert Date from yyyy-mm-day to an array(yyyy, mon , day)
    birthDate = convertObj.convertDate(studentInfo[10].text)    
    monthOfDate = convertObj.convertString(birthDate[1])
    selenium.select "birth_date[M]", monthOfDate

    dayOfDate = convertObj.convertString(birthDate[2])
    selenium.select "birth_date[d]", dayOfDate

    yearOfDate = convertObj.convertString(birthDate[0])
    selenium.select "birth_date[Y]", yearOfDate

    if studentInfo[30].text  then
      sibling = convertObj.convertString(studentInfo[30].text)
      selenium.select "number_siblings", sibling  
    else
      selenium.select "number_siblings", "label=-select-" 
    end
    selenium.type   "location[1][address][street_address]", studentInfo[12].text
    selenium.type   "location[1][address][supplemental_address_1]", "" 
    selenium.type   "location[1][address][city]", studentInfo[13].text  

    state =  convertObj.convertString(studentInfo[14].text)
    selenium.select "location[1][address][state_province_id]", state

    selenium.type   "location[1][address][postal_code]", studentInfo[15].text
    selenium.type   "location[1][address][postal_code_suffix]", studentInfo[16].text 

    country =  convertObj.convertString(studentInfo[17].text)
    selenium.select "location[1][address][country_id]",country
    
    selenium.type   "location[1][phone][1][phone]",studentInfo[18].text

    selenium.type "location[2][address][street_address]", studentInfo[21].text 
    selenium.type "location[2][address][supplemental_address_1]", " "
    selenium.type "location[2][address][city]", studentInfo[22].text 

    state1 =  convertObj.convertString(studentInfo[23].text)
    selenium.select "location[2][address][state_province_id]",state1

    selenium.type "location[2][address][postal_code]", studentInfo[24].text
    selenium.type "location[2][address][postal_code_suffix]",studentInfo[25].text
    
    country1 =  convertObj.convertString(studentInfo[26].text)
    selenium.select "location[2][address][country_id]", country1
  
    ethenticity =  convertObj.convertString(studentInfo[35].text)
    selenium.select "ethnicity_id_1", ethenticity
    
    citizenship = convertObj.convertString(studentInfo[37].text)
    selenium.select "citizenship_status_id", citizenship

    #selenium.type "tribe_affiliation", studentIfo[].text
    #date = convertObj.convertDate(studentInfo[10].text) 
    #monthOfDate = convertObj.convertString(date[1])
    #selenium.select "tribe_enroll_date[M]", monthOfDate
  
    #yearOfDate = convertObj.convertString(date[0])
    #selenium.select "tribe_enroll_date[Y]", yearOfDate
    
    if studentInfo[27].text == "Rural" then
      selenium.check "home_area_id"
    elsif studentInfo[27].text == "Suburban" then
      selenium.check "document.Personal.home_area_id[1]"
    elsif studentInfo[27].text == "Urban" then
      selenium.check "document.Personal.home_area_id[2]"
    end

    # selenium.select "growup_country_id",  studentInfo[58].text  #"label=United States"
    selenium.type   "years_in_us", studentInfo[27].text 
    selenium.type   "first_language", studentInfo[33].text
    selenium.type   "primary_language", studentInfo[34].text 

    grad_date = convertObj.convertDate(studentInfo[29].text) 
    selenium.select "high_school_grad_year[Y]",grad_date[0]
  end
end


#class to add Additional Information
class CPSAdditionalInformation
  def additionalDetails(selenium, additionalInfo)

    selenium.select "fed_lunch_id", additionalInfo[22].text #"label=Yes Free Lunches"
    if additionalInfo[52].text == "1" then
      selenium.check "financial_aid_applicant"
    end
    if additionalInfo[61].text == "x" then
      selenium.check "parent_grad_college_id"
    end
    
    if additionalInfo[64].text == "x" then
      selenium.check "is_dismissed"
      selenium.type "explain_dismissed", "dismissed "
    else
      selenium.check "document.Scholarship.is_dismissed[1]"
    end
    
    if additionalInfo[65].text == "x" then
      selenium.check "is_convicted"
      selenium.type "explain_convicted", "convicted"
    else
      selenium.check "document.Scholarship.is_convicted[1]"
    end
    
    if additionalInfo[63].text == "x" then
      selenium.check "is_health_insurance"
    end
    
    selenium.type "displacement", ""
   
    #contact information for current juniors that you think would be strong applicants for next year.
    selenium.type "referral_student_first_name_1", ""
    selenium.type "referral_student_last_name_1", ""
    selenium.type "referral_student_school_1", ""
    selenium.select "referral_student_year_1[Y]", "label=-year-"
    selenium.type "referral_student_email_1", ""
    selenium.type "referral_student_phone_1", ""

    selenium.type "referral_student_first_name_2", ""
    selenium.type "referral_student_last_name_2", ""
    selenium.type "referral_student_school_2", ""
    selenium.select "referral_student_year_2[Y]", "label=-year-"
    selenium.type "referral_student_email_2", ""
    selenium.type "referral_student_phone_2", ""

    selenium.type "referral_student_first_name_3", ""
    selenium.type "referral_student_last_name_3", ""
    selenium.type "referral_student_school_3", ""
    selenium.select "referral_student_year_3[Y]", "label=-year-"
    selenium.type "referral_student_email_3", ""
    selenium.type "referral_student_phone_3", ""

    #Enter contact information for 3 teachers/counselors in your local area who you think would be helpful in identifying students
    selenium.type "referral_educator_first_name_1", ""
    selenium.type "referral_educator_last_name_1", ""
    selenium.type "referral_educator_school_1", ""
    selenium.select "referral_educator_position_id_1", "label=- select -"
    selenium.type "referral_educator_email_1", ""
    selenium.type "referral_educator_phone_1", ""
    
    selenium.type "referral_educator_first_name_2", ""
    selenium.type "referral_educator_last_name_2", ""
    selenium.type "referral_educator_school_2", ""
    selenium.select "referral_educator_position_id_2", "label=- select -"
    selenium.type "referral_educator_email_2", ""
    selenium.type "referral_educator_phone_2", ""

    selenium.type "referral_educator_first_name_3", ""
    selenium.type "referral_educator_last_name_3", ""
    selenium.type "referral_educator_school_3", ""
    selenium.select "referral_educator_position_id_3", "label=- select -"
    selenium.type "referral_educator_email_3", ""
    selenium.type "referral_educator_phone_3", ""
    
  end
end



#class to add Educational Information
class CPSEducationalInformation 
  def educationalDetails(selenium, interestDetails, doc)
    #select Educational interest
    educational = Array.new()
    educational = (interestDetails[0].text).split(',')

    educational1 = Array.new()
    educational.each{|a|
      str = String.new(a)
      a1 = str.gsub(/\s/, '')
      educational1 = educational1.push(a1)
    }

    educational1.each{|a|
      if a == "Business" then
        selenium.check "educational_interest[237]"
      end
    }
    educational1.each{|a|
      if a == "Education" then
        selenium.check "educational_interest[238]"
      end
    }
    educational1.each{|a|
      if a == "Engineering/Technology" then
        selenium.check "educational_interest[239]"
      end
    }
    educational1.each{|a|
      if a == "Fine Arts" then
        selenium.check "educational_interest[240]"
      end
    }
    educational1.each{|a|
      if a == "Liberal Arts" then
        selenium.check "educational_interest[241]"
      end
    }
    educational1.each{|a|
      if a == "Pre-Law" then
        selenium.check "educational_interest[242]"
      end
    }
    educational1.each{|a|
      if a == "Pre-Med" then
        selenium.check "educational_interest[243]"
      end
    }
    educational1.each{|a|
      if a == "Sciences" then
        selenium.check "educational_interest[244]"
      end
    }
    educational1.each{|a|
      if a == "Other" then
        selenium.check "educational_interest[245]"
        selenium.type "educational_interest_other", "Maths"
      end
    }
 
    #select College Type
    educational1.each{|a|
      if a == "Liberal arts college" then
        selenium.check "college_type[246]" 
      end
    }
    educational1.each{|a|
      if a == "Engineering/Technology focused school" then
        selenium.check "college_type[247]"
      end
    }
    educational1.each{|a|
      if a == "Public or private university" then
        selenium.check "college_type[248]"
      end
    }
    educational1.each{|a|
      if a == "Single gender school" then
        selenium.check "college_type[249]"
      end
    }
    educational1.each{|a|
      if a == "Religiously affiliated school" then
        selenium.check "college_type[250]"
      end
    }
    educational1.each{|a|
      if a == "Does not matter" then
        selenium.check "college_type[251]"
      end
    }
    educational1.each{|a|
      if a == "MIT" then
        selenium.check "college_type[252]"
      end
    }
    educational1.each{|a|
      if a == "Yale University" then
        selenium.check "college_type[253]"
      end
    }
    
  end
end

#class to add Extracurricular Information
class CPSExtracurricularInformation 
  def extraCurricularDetails(selenium, curricularInfo, doc)
    
    #Extracurricular, Volunteer, and Personal Activities

    selenium.type "activity_1", curricularInfo[3].text
    
    if curricularInfo[4].text =="x" then 
      selenium.check "grade_level_1_1"
    else
      selenium.uncheck "grade_level_1_1"
    end

    if curricularInfo[5].text =="x" then 
      selenium.check "grade_level_2_1"
    else
      selenium.uncheck "grade_level_2_1"
    end

    if curricularInfo[6].text =="x" then 
      selenium.check "grade_level_3_1"
    else
      selenium.uncheck "grade_level_3_1"
    end

    selenium.type "time_spent_1_1", curricularInfo[10].text
    selenium.type "time_spent_2_1", curricularInfo[11].text
    selenium.type "positions_1",    curricularInfo[9].text

    selenium.type "activity_2",     curricularInfo[12].text

    if curricularInfo[13].text =="x" then 
      selenium.check "grade_level_1_2"     
    else
      selenium.uncheck "grade_level_1_2"     
    end

    if curricularInfo[14].text =="x" then 
      selenium.check "grade_level_2_2"
    else
      selenium.uncheck "grade_level_2_2"
    end

    if curricularInfo[15].text =="x" then 
      selenium.check "grade_level_3_2"
    else
      selenium.uncheck "grade_level_3_2"
    end

    selenium.type "time_spent_1_2", curricularInfo[19].text
    selenium.type "time_spent_2_2", curricularInfo[20].text
    selenium.type "positions_2",    curricularInfo[18].text

    selenium.type "activity_3",     curricularInfo[21].text

    if curricularInfo[22].text =="x" then 
      selenium.check "grade_level_1_3"     
    else
      selenium.uncheck "grade_level_1_3"     
    end

    if curricularInfo[23].text =="x" then 
      selenium.check "grade_level_2_3"
    else
      selenium.uncheck "grade_level_2_3"
    end

    if curricularInfo[24].text =="x" then 
      selenium.check "grade_level_3_3"
    else
      selenium.uncheck "grade_level_3_3"
    end

    selenium.type "time_spent_1_3", curricularInfo[28].text
    selenium.type "time_spent_2_3", curricularInfo[29].text
    selenium.type "positions_3",    curricularInfo[27].text

    selenium.type "activity_4",     curricularInfo[30].text

    if curricularInfo[31].text =="x" then 
      selenium.check "grade_level_1_4"     
    else
      selenium.uncheck "grade_level_1_4"     
    end

    if curricularInfo[32].text =="x" then 
      selenium.check "grade_level_2_4"
    else
      selenium.uncheck "grade_level_2_4"
    end

    if curricularInfo[33].text =="x" then 
      selenium.check "grade_level_3_4"
    else
      selenium.uncheck "grade_level_3_4"
    end

    selenium.type "time_spent_1_4", curricularInfo[37].text
    selenium.type "time_spent_2_4", curricularInfo[38].text
    selenium.type "positions_4",    curricularInfo[36].text


    selenium.type "activity_5",     curricularInfo[39].text

    if curricularInfo[40].text =="x" then 
      selenium.check "grade_level_1_5"     
    else
      selenium.uncheck "grade_level_1_5"     
    end

    if curricularInfo[41].text =="x" then 
      selenium.check "grade_level_2_5"
    else
      selenium.uncheck "grade_level_2_5"
    end

    if curricularInfo[42].text =="x" then 
      selenium.check "grade_level_3_5"
    else
      selenium.uncheck "grade_level_3_5"
    end


    selenium.type "time_spent_1_5", curricularInfo[46].text
    selenium.type "time_spent_2_5", curricularInfo[47].text
    selenium.type "positions_5",    curricularInfo[45].text

    selenium.type "time_spent_1_6", curricularInfo[50].text
    selenium.type "time_spent_2_6", curricularInfo[51].text
    selenium.type "positions_6",    curricularInfo[49].text

    selenium.type "time_spent_1_7", curricularInfo[54].text
    selenium.type "time_spent_2_7", curricularInfo[55].text
    selenium.type "positions_7",    curricularInfo[53].text

    #Describe which single activity/interest listed above represents your most meaningful commitment and why? (50 words max)
    selenium.type "essay[meaningful_commitment]", curricularInfo[0].text
    
    #List and describe your activities, including jobs, during the past two summers. (50 words max
    selenium.type "essay[past_activities]", curricularInfo[1].text
    #Express your interests.
    selenium.type "essay[hobbies]", curricularInfo[2].text

    #Are you interested in participating in either of the following in college?
    if curricularInfo[48].text then
      selenium.uncheck "varsity_sports"
    else
      selenium.uncheck "varsity_sports"
      selenium.type "varsity_sports_list", curricularInfo[48].text
    end
    
    if curricularInfo[48].text then
      selenium.uncheck "arts"
    else
      selenium.check "arts"
      selenium.type "arts_list", curricularInfo[49].text
    end

  end
end

#class to add Work experience details
class CPSWorkExperienceInformation
  def workExperienceDetails(selenium, workDetails,doc)
    #List any job you have held during the past three years.
    selenium.type "nature_of_work_1", workDetails[0].text
    selenium.type "employer_1", workDetails[1].text

    convertObj=CPSConvert.new()
    startDate = convertObj.convertDate(workDetails[2].text)    
    endDate =   convertObj.convertDate(workDetails[3].text)    

    start_monthDate =  convertObj.convertString(startDate[1])
    selenium.select "start_date_1[M]", start_monthDate

    start_yearDate =  convertObj.convertString(startDate[0])
    selenium.select "start_date_1[Y]", start_yearDate
    
    end_monthDate =  convertObj.convertString(endDate[1])
    selenium.select "end_date_1[M]", end_monthDate

    end_yearDate =  convertObj.convertString(endDate[0])
    selenium.select "end_date_1[Y]", end_yearDate

    selenium.type "hrs_1", workDetails[4].text
    
    selenium.uncheck "summer_jobs_1"
    
    selenium.type "nature_of_work_2", workDetails[5].text
    selenium.type "employer_2", workDetails[6].text

    startDate = convertObj.convertDate(workDetails[7].text)    
    endDate =  convertObj.convertDate(workDetails[8].text)    

    start_monthDate =  convertObj.convertString(startDate[1])
    selenium.select "start_date_2[M]", start_monthDate

    start_yearDate =  convertObj.convertString(startDate[0])
    selenium.select "start_date_2[Y]",start_yearDate

    end_monthDate =  convertObj.convertString(endDate[1])
    selenium.select "end_date_2[M]", end_monthDate
    end_yearDate =  convertObj.convertString(endDate[0])
    selenium.select "end_date_2[Y]", end_yearDate
    selenium.type "hrs_2", workDetails[9].text

    selenium.uncheck "summer_jobs_2"

    selenium.type "nature_of_work_3", workDetails[10].text
    selenium.type "employer_3", workDetails[11].text

    startDate = convertObj.convertDate(workDetails[12].text)    
    endDate =  convertObj.convertDate(workDetails[13].text)    

    start_monthDate =  convertObj.convertString(startDate[1])
    selenium.select "start_date_3[M]", start_monthDate

    start_yearDate =  convertObj.convertString(startDate[0])
    selenium.select "start_date_3[Y]", start_yearDate

    end_monthDate =  convertObj.convertString(endDate[1])
    selenium.select "end_date_3[M]", end_monthDate

    end_yearDate =  convertObj.convertString(endDate[0])
    selenium.select "end_date_3[Y]", end_yearDate
    
    selenium.type "hrs_3", workDetails[14].text

    selenium.uncheck "summer_jobs_3"

    selenium.type "nature_of_work_4", workDetails[15].text
    selenium.type "employer_4", workDetails[16].text

    startDate = convertObj.convertDate(workDetails[17].text)    
    endDate =  convertObj.convertDate(workDetails[18].text)    

    start_monthDate =  convertObj.convertString(startDate[1])
    selenium.select "start_date_4[M]", start_monthDate
    start_yeraDate =  convertObj.convertString(startDate[0])
    selenium.select "start_date_4[Y]", start_yearDate

    end_monthDate =  convertObj.convertString(endDate[1])
    selenium.select "end_date_4[M]", end_monthDate
    end_yearDate =  convertObj.convertString(endDate[0])
    selenium.select "end_date_4[Y]", end_yearDate
    selenium.type "hrs_4", workDetails[19].text

    selenium.uncheck "summer_jobs_4"

    selenium.type "nature_of_work_5", workDetails[20].text
    selenium.type "employer_5", workDetails[21].text

    startDate = convertObj.convertDate(workDetails[22].text)    
    endDate =  convertObj.convertDate(workDetails[23].text)    

    start_monthDate =  convertObj.convertString(startDate[1])
    selenium.select "start_date_5[M]", start_monthDate
    selenium.select "start_date_5[Y]", startDate[0]

    end_monthDate =  convertObj.convertString(startDate[1])
    selenium.select "end_date_5[M]", end_monthDate
    end_yearDate =  convertObj.convertString(endDate[0])
    selenium.select "end_date_5[Y]", end_yearDate
    selenium.type "hrs_5", workDetails[24].text

    selenium.uncheck "summer_jobs_5"

    #To what use have you put your earnings?
    selenium.type "earnings", "  "
    selenium.check "document.WorkExperience.school_work[1]"
  end
end


#class to add Household Information
class CPSHouseholdInformation 
  def householdDetails(selenium, householdDetails, doc)
    #get relationshp id
    convertObj = CPSConvert.new()
    selenium.type "member_count_1", householdDetails[0].text
    
    personName = Array.new()
  
    #get Person Name 
    if doc.get_elements("StudentDetail/Household_Current/Person_1").collect{|person|
      person.each_element{|p| 
        personName = personName.push(p)
      }
    }
    end
    selenium.type "first_name_1_1", personName[1].text
    selenium.type "last_name_1_1", personName[2].text

  
    relationship = convertObj.convertString(householdDetails[16].text)
    selenium.select "relationship_id_1_1", relationship

    relationship = convertObj.convertString(householdDetails[17].text)
    selenium.select "relationship_id_1_2", relationship

    #get Person Name 
    personName = Array.new()
    if doc.get_elements("StudentDetail/Household_Current/Person_2").collect{|person1|
      person1.each_element{|p1| 
        personName = personName.push(p1)
      }
    }
    end
    selenium.type "first_name_1_2", personName[1].text
    selenium.type "last_name_1_2", personName[2].text

   
    #add no of yrs lived
    yearsLived = convertObj.convertString(householdDetails[2].text)
    selenium.select "years_lived_id_1",yearsLived
    
    selenium.check "same_2_1"
    selenium.check "same_2_2"

    relationship = convertObj.convertString(householdDetails[16].text)
    selenium.select "relationship_id_2_1", relationship

    relationship = convertObj.convertString(householdDetails[17].text)
    selenium.select "relationship_id_2_2", relationship
    
    selenium.select "years_lived_id_2",householdDetails[2].text
    #if lived less than 5 yrs then add other details
    #selenium.type "member_count_2", householdDetails[10].text

    #personName = Array.new()

    #selected a specific informatrion
    #convertObj = CPSConvert.new()
    #relationship = convertObj.convertString()
    #selenium.select "relationship_id_2_1", "label=Father"

    #get name of the person
    #if doc.get_elements("StudentDetail/Household_Previous/Person_1").collect{|person|
    #    person.each_element{|p| 
    #      personName = personName.push(p)
    #    }
    #  }
    #end
    #selenium.type "first_name_2_1", personName[1]
    #selenium.type "last_name_2_1", personName[2]
    
    #if selected specific details
    #relationship = convertObj.convertString()
    #selenium.select "relationship_id_2_2", "label=Grandfather"
    
    #get person name
    #if doc.get_elements("StudentDetail/Household_Previous/Person_2").collect{|person|
    #    person.each_element{|p| 
    #      personName = personName.push(p)
    #    }
    #  }
    #end
    #selenium.type "first_name_2_2", personName[1]
    #selenium.type "last_name_2_2", personaName[2]
        
    #read no of yrs lived
    #selenium.select "years_lived_id_2",householdDetails[10].text
    
    #add description
    selenium.type "description", householdDetails[1].text
  end
end
#class to add Parent/Guardian Information
class CPSParentInformation 
  def motherDetails(selenium,guardianInfo,doc)
    convertObj = CPSConvert.new()

    maritalStatus = convertObj.convertString(guardianInfo[28].text)
    selenium.select "marital_status_id", maritalStatus
    
    if guardianInfo[3].text == "1" then
      selenium.check "//input[@name='is_deceased' and @value='1']"
      date = convertObj.convertString(guardianInfo[24].text)
      yearDate = convertObj.convertDate(date[0])
      selenium.select "deceased_year_date[Y]", yearDate
    else
      selenium.check "//input[@name='is_deceased' and @value='0']"
    end
    
    date = Array.new()
    date = convertObj.convertDate(guardianInfo[23].text)
    
    month_date = convertObj.convertString(date[1])
    selenium.select "birth_date[M]", month_date

    day_date = convertObj.convertString(date[2])
    selenium.select "birth_date[d]", day_date

    year_date = convertObj.convertString(date[0])
    selenium.select "birth_date[Y]", year_date
    
    selenium.check "citizenship_status"
    country = convertObj.convertString(guardianInfo[22].text)
    selenium.select "citizenship_country_id", "label=United States"

    if guardianInfo[12].text then
      selenium.check "document.forms['Guardian-Mother'].all_life[1]"
      selenium.type "lived_with_from_age", guardianInfo[12].text
      selenium.type "lived_with_to_age", guardianInfo[13].text
    else
      selenium.check "all_life"
    end

    selenium.check "copy_address"
    #selenium.type "location[1][address][street_address]", "ddfasd"
    #selenium.type "location[1][address][supplemental_address_1]", "ddfsdf"
    #selenium.type "location[1][address][city]", "dfsdf"
    #selenium.select "location[1][address][state_province_id]", "label=Florida"
    #selenium.type "location[1][address][postal_code]", "34234"
    #selenium.type "location[1][address][postal_code_suffix]", "34233"
    #selenium.select "location[1][address][country_id]", "label=Aruba"
    
    selenium.check "copy_phone"
    #selenium.type "location[1][phone][1][phone]", "32424"
    
    industry = convertObj.convertString(guardianInfo[29].text)
    selenium.select "industry_id", industry
    
    selenium.type "job_organization", guardianInfo[4].text
    
    selenium.type "job_occupation", guardianInfo[5].text
    
    selenium.type "job_current_years", guardianInfo[6].text
    
    schoolLevel = convertObj.convertString(guardianInfo[30].text)
    selenium.select "highest_school_level_id", schoolLevel

    selenium.type "description", guardianInfo[14].text
  end
  
  def fatherDetails(selenium, guardianInfo,doc)
    convertObj = CPSConvert.new()
    maritalStatus = convertObj.convertString(guardianInfo[28].text)
    selenium.select "marital_status_id", maritalStatus
    
    if guardianInfo[3].text == "1" then
      selenium.check "//input[@name='is_deceased' and @value='1']"
      date = convertObj.convertString(guardianInfo[24].text)
      yearDate = convertObj.convertDate(date[0])
      selenium.select "deceased_year_date[Y]", yearDate
    else
      selenium.check "//input[@name='is_deceased' and @value='0']"
    end
    
    date = Array.new()
    date = convertObj.convertDate(guardianInfo[23].text)
    
    month_date = convertObj.convertString(date[1])
    selenium.select "birth_date[M]", month_date

    day_date = convertObj.convertString(date[2])
    selenium.select "birth_date[d]", day_date

    year_date = convertObj.convertString(date[0])
    selenium.select "birth_date[Y]", year_date
    selenium.check "citizenship_status"
    #country = convertObj.convertString(guardianInfo[22].text)
    selenium.select "citizenship_country_id", "label=United States"

    if guardianInfo[12].text then
      selenium.check "document.forms['Guardian-Mother'].all_life[1]"
      selenium.type "lived_with_from_age", guardianInfo[12].text
      selenium.type "lived_with_to_age", guardianInfo[13].text
    else
      selenium.check "all_life"
    end

    selenium.check "copy_address"
    #selenium.type "location[1][address][street_address]", "ddfasd"
    #selenium.type "location[1][address][supplemental_address_1]", "ddfsdf"
    #selenium.type "location[1][address][city]", "dfsdf"
    #selenium.select "location[1][address][state_province_id]", "label=Florida"
    #selenium.type "location[1][address][postal_code]", "34234"
    #selenium.type "location[1][address][postal_code_suffix]", "34233"
    #selenium.select "location[1][address][country_id]", "label=Aruba"
    
    selenium.check "copy_phone"
    #selenium.type "location[1][phone][1][phone]", "32424"
    
    industry = convertObj.convertString(guardianInfo[29].text)
    selenium.select "industry_id", industry
    
    selenium.type "job_organization", guardianInfo[4].text
    
    selenium.type "job_occupation", guardianInfo[5].text
    
    selenium.type "job_current_years", guardianInfo[6].text
    
    schoolLevel = convertObj.convertString(guardianInfo[30].text)
    selenium.select "highest_school_level_id", schoolLevel

    selenium.type "description", guardianInfo[14].text
  end
end

#class to add Sibling Information
class CPSIncomeInformation 
  def income_1_Details(selenium, incomeDetails, doc)
    convertObj = CPSConvert.new()
    incomeType = convertObj.convertString(incomeDetails[2].text)
   
    selenium.select "type_of_income_id_1",incomeType
    selenium.type "job_1",incomeDetails[2].text
    selenium.type "amount_1", incomeDetails[3].text

  end

  def income_2_Details(selenium, incomeDetails, doc)
    convertObj = CPSConvert.new()
    incomeType = convertObj.convertString(incomeDetails[2].text)
   
    selenium.select "type_of_income_id_1",incomeType
    selenium.type "job_1",incomeDetails[2].text
    selenium.type "amount_1", incomeDetails[3].text
  end
end

class CPSSiblingInformation
  def sibling_1_details(selenium, siblingDetails)
    convertObj = CPSConvert.new
     
    selenium.type "document.forms['Sibling-1'].first_name", siblingDetails[1].txt
    selenium.type "document.forms['Sibling-1'].last_name", siblingDetails[2].txt
    relation = convertObj.convertString(siblingDetails[27].txt) 
    selenium.select "sibling_relationship_id",  relation

    date = convertObj.convertDate(siblingDetails[23].txt) 
    month_date = convertObj.convertString(date[1]) 
    selenium.select "birth_date[M]", month_date

    day_date = convertObj.convertString(date[2]) 
    selenium.select "birth_date[d]", day_date

    year_date = convertObj.convertString(date[0]) 
    selenium.select "birth_date[Y]", year_date
    
    selenium.click "document.forms['Sibling-1'].all_life[1]"
    selenium.type "lived_with_from_age", siblingDetails[12].txt
    selenium.type "lived_with_to_age", siblingDetails[13].txt
      
    current_school = convertObj.convertString(siblingDetails[31].txt) 
    selenium.select "current_school_level_id", current_school  #"label=Junior High School"
    
    if siblingDetails[31].txt == "4 Year College" || siblingDetails[31].txt == "Graduate School Degree (Masters)" then
      current_school = convertObj.convertString(siblingDetails[31].txt) 
      selenium.select "current_school_level_id", current_school  #"label=Junior High School"

      highest_school = convertObj.convertString(siblingDetails[30].txt) 
      selenium.select "highest_school_level_id", highest_school #"label=Some High School"
      
      college_country = convertObj.convertString(siblingDetails[26].txt) 

      selenium.select "college_country_id", college_country

      year_date = convertObj.convertString(siblingDetails[9].txt) 
      selenium.select "college_grad_year[Y]", year_date #"label=1959"
      
      selenium.type "college_major", siblingDetails[10].txt
    end
    
    highest_school = convertObj.convertString(siblingDetails[30].txt) 
    selenium.select "highest_school_level_id", highest_school #"label=Some High School"
    
    selenium.type "college_name",siblingDetails[8].txt 
    
    selenium.type "job_occupation", siblingDetails[5].txt

    selenium.type "description", siblingDetails[14].txt
  end
  
  def sibling_2_details(selenium, siblingDetails)
    convertObj = CPSConvert.new
  
    selenium.type "document.forms['Sibling-1'].first_name", siblingDetails[1].txt
    selenium.type "document.forms['Sibling-1'].last_name", siblingDetails[2].txt
    relation = convertObj.convertString(siblingDetails[27].txt) 
    selenium.select "sibling_relationship_id",  relation

    date = convertObj.convertDate(siblingDetails[23].txt) 
    month_date = convertObj.convertString(date[1]) 
    selenium.select "birth_date[M]", month_date

    day_date = convertObj.convertString(date[2]) 
    selenium.select "birth_date[d]", day_date

    year_date = convertObj.convertString(date[0]) 
    selenium.select "birth_date[Y]", year_date
    
    selenium.click "document.forms['Sibling-1'].all_life[1]"
    selenium.type "lived_with_from_age", siblingDetails[12].txt
    selenium.type "lived_with_to_age", siblingDetails[13].txt
      
    current_school = convertObj.convertString(siblingDetails[31].txt) 
    selenium.select "current_school_level_id", current_school  #"label=Junior High School"
    
    if siblingDetails[31].txt == "4 Year College" || siblingDetails[31].txt == "Graduate School Degree (Masters)" then
      current_school = convertObj.convertString(siblingDetails[31].txt) 
      selenium.select "current_school_level_id", current_school  #"label=Junior High School"

      highest_school = convertObj.convertString(siblingDetails[30].txt) 
      selenium.select "highest_school_level_id", highest_school #"label=Some High School"
      
      college_country = convertObj.convertString(siblingDetails[26].txt) 

      selenium.select "college_country_id", college_country

      year_date = convertObj.convertString(siblingDetails[9].txt) 
      selenium.select "college_grad_year[Y]", year_date #"label=1959"
      
      selenium.type "college_major", siblingDetails[10].txt
    end
    
    highest_school = convertObj.convertString(siblingDetails[30].txt) 
    selenium.select "highest_school_level_id", highest_school #"label=Some High School"
    
    selenium.type "college_name",siblingDetails[8].txt 
    
    selenium.type "job_occupation", siblingDetails[5].txt

    selenium.type "description", siblingDetails[14].txt
  end
end
#class to add High School Information
class CPSHighschoolInformation 
  def highSchoolDetails(selenium, schoolInfo, doc)
    
    convertObj = CPSConvert.new
    # assert_equal "Find Your School", @selenium.get_value("//input[@id='btn-school-search']")
    # if selenium.click "//input[@id='btn-school-search']"
    # selenium.wait_for_pop_up "schoolSearch", "30000"
    # selenium.select_window "schoolSearch"
    # selenium.type "school_name", "st joseph"
    # selenium.select "state_province_id", "label=California"
    # selenium.type "city", "sant joes"
    # selenium.type "postal_code", "234"
    # selenium.click "_qf_SchoolSearch_refresh"
    # assert_equal "Close Window", @selenium.get_value("//input[@id='btn-close-window']")
    # selenium.click "//input[@id='btn-close-window']"
    # end
    
    #enter current high school information
    selenium.type "organization_name_1", schoolInfo[0].text
    selenium.type "custom_1_1", schoolInfo[1].text
    selenium.type "location_1[1][address][street_address]", schoolInfo[8].text
    selenium.type "location_1[1][address][city]",  schoolInfo[9].text

    state = convertObj.convertString(schoolInfo[10].text) 

    selenium.select "location_1[1][address][state_province_id]", state
    selenium.type "location_1[1][address][postal_code]", schoolInfo[11].text
    selenium.type "location_1[1][address][postal_code_suffix]", schoolInfo[12].text

    country = convertObj.convertString(schoolInfo[13].text)
    selenium.select "location_1[1][address][country_id]", country  

    schoolType = convertObj.convertString(schoolInfo[2].text)
    selenium.select "custom_2_1", schoolType 

    selenium.type "location_1[1][phone][1][phone]", schoolInfo[14].text

    entryDate  = convertObj.convertDate(schoolInfo[5].text)
    entryDate1 = convertObj.convertString(entryDate[1])
    selenium.select "date_of_entry_1[M]", entryDate1  #"label=Mar"
    
    entryDate2 = convertObj.convertString(entryDate[0])
    selenium.select "date_of_entry_1[Y]", entryDate2 

    exitDate  = convertObj.convertDate(schoolInfo[6].text)
    exitDate1 =  convertObj.convertString(exitDate[1])
    selenium.select "date_of_exit_1[M]", exitDate1

    exitDate2 =  convertObj.convertString(exitDate[0])
    selenium.select "date_of_exit_1[Y]", exitDate2

    selenium.type "custom_3_1", schoolInfo[3].text
    
    str = convertObj.checkString(schoolInfo[16].text)
    
    if ! str then
      #Add School 2 Information
      selenium.click "HighSchool_2_show"
      
      #enter Previous High School information
      selenium.type "organization_name_2", schoolInfo[16].text
      selenium.type "custom_1_2", schoolInfo[17].text
      
      selenium.type "location_2[1][address][street_address]", schoolInfo[24].text
      selenium.type "location_2[1][address][city]", schoolInfo[25].text
      
      state = convertObj.convertString(schoolInfo[26].text) 
      selenium.select "location_2[1][address][state_province_id]", state
      selenium.type "location_2[1][address][postal_code]", schoolInfo[27].text
      selenium.type "location_2[1][address][postal_code_suffix]", schoolInfo[28].text
      
      country = convertObj.convertString(schoolInfo[29].text)
      selenium.select "location_2[1][address][country_id]", country
      
      schoolType = convertObj.convertString(schoolInfo[18].text)
      selenium.select "custom_2_2", schoolType
      selenium.type "location_2[1][phone][1][phone]",schoolInfo[30].text
      
      entryDate  = convertObj.convertDate(schoolInfo[21].text)
      entryDate1 = convertObj.convertString(entryDate[1])
      selenium.select "date_of_entry_2[M]", entryDate1
      
      entryDate2 = convertObj.convertString(entryDate[0])
      selenium.select "date_of_entry_2[Y]", entryDate2
      
      exitDate  = convertObj.convertDate(schoolInfo[22].text)
      exitDate1 =  convertObj.convertString(exitDate[1])
      selenium.select "date_of_exit_2[M]", exitDate1
    
      exitDate2 =  convertObj.convertString(exitDate[0])
      selenium.select "date_of_exit_2[Y]", exitDate2

      selenium.type "custom_3_2", schoolInfo[19].text
    end

    str = convertObj.checkString(schoolInfo[32].text)
    if ! str then
      selenium.click "HighSchool_3_show"
      #enter another Previous High School
      selenium.type "organization_name_3", schoolInfo[32].text
      selenium.type "custom_1_3", schoolInfo[33].text
      
      selenium.type "location_3[1][address][street_address]", schoolInfo[40].text
      selenium.type "location_3[1][address][city]", schoolInfo[41].text
      
      state = convertObj.convertString(schoolInfo[42].text) 
      selenium.select "location_3[1][address][state_province_id]", state
      
      selenium.type "location_3[1][address][postal_code]", schoolInfo[43].text
      selenium.type "location_3[1][address][postal_code_suffix]",schoolInfo[44].text
    
      country = convertObj.convertString(schoolInfo[45].text)
      selenium.select "location_3[1][address][country_id]", country
      
      schoolType = convertObj.convertString(schoolInfo[34].text)
      selenium.select "custom_2_3", schoolType
      
      selenium.type "location_3[1][phone][1][phone]", schoolInfo[46].text
      
      entryDate  = convertObj.convertDate(schoolInfo[37].text)
      entryDate1 = convertObj.convertString(entryDate[1])
      selenium.select "date_of_entry_3[M]", entryDate1
      
      entryDate2 = convertObj.convertString(entryDate[0])
      selenium.select "date_of_entry_3[Y]", entryDate2
      
      exitDate  = convertObj.convertDate(schoolInfo[38].text)
      exitDate1 =  convertObj.convertString(exitDate[1])
      selenium.select "date_of_exit_3[M]", exitDate1
      
      exitDate1 =  convertObj.convertString(exitDate[0])
      selenium.select "date_of_exit_3[Y]", exitDate2
      
      selenium.type "custom_3_3", schoolInfo[35].text
    end
  end
end

#class to add Other School Information
class CPSOtherSchoolInformation 
  def otherSchoolDetails(selenium, otherSchoolInfo, doc)

    convertObj = CPSConvert.new

    #Other school information 1
    selenium.type "organization_name_1", otherSchoolInfo[0].text

    entryDate  = convertObj.convertDate(otherSchoolInfo[5].text)
    entryDate1 = convertObj.convertString(entryDate[1])
    selenium.select "date_of_entry_1[M]", entryDate1 

    entryDate2 = convertObj.convertString(entryDate[0])
    selenium.select "date_of_entry_1[Y]", entryDate2

    exitDate  = convertObj.convertDate(otherSchoolInfo[6].text)
    exitDate1 =  convertObj.convertString(exitDate[1])
    selenium.select "date_of_exit_1[M]",exitDate1 

    exitDate2 =  convertObj.convertString(exitDate[0])
    selenium.select "date_of_exit_1[Y]", exitDate2

    selenium.type "location_1[1][address][city]", otherSchoolInfo[9].text
    selenium.select "location_1[1][address][state_province_id]", otherSchoolInfo[10].text
    selenium.select "location_1[1][address][country_id]", otherSchoolInfo[13].text
    selenium.type "note_1",otherSchoolInfo[7].text 
    
    #Other school information 2
    selenium.type "organization_name_2", otherSchoolInfo[16].text

    entryDate  = convertObj.convertDate(otherSchoolInfo[21].text)
    entryDate1 = convertObj.convertString(entryDate[1])
    selenium.select "date_of_entry_2[M]", entryDate1 

    entryDate2 = convertObj.convertString(entryDate[0])
    selenium.select "date_of_entry_2[Y]", entryDate2

    exitDate  = convertObj.convertDate(otherSchoolInfo[22].text)
    exitDate1 =  convertObj.convertString(exitDate[1])
    selenium.select "date_of_exit_2[M]",exitDate1 

    exitDate2 =  convertObj.convertString(exitDate[0])
    selenium.select "date_of_exit_2[Y]", exitDate2

    selenium.type "location_2[1][address][city]", otherSchoolInfo[25].text
    selenium.select "location_2[1][address][state_province_id]", otherSchoolInfo[26].text
    selenium.select "location_2[1][address][country_id]", otherSchoolInfo[29].text
    selenium.type "note_2",otherSchoolInfo[23].text 
    

   #Other school information 3
    selenium.type "organization_name_3", otherSchoolInfo[32].text

    entryDate  = convertObj.convertDate(otherSchoolInfo[37].text)
    entryDate1 = convertObj.convertString(entryDate[1])
    selenium.select "date_of_entry_3[M]", entryDate1 

    entryDate2 = convertObj.convertString(entryDate[0])
    selenium.select "date_of_entry_3[Y]", entryDate2

    exitDate  = convertObj.convertDate(otherSchoolInfo[38].text)
    exitDate1 =  convertObj.convertString(exitDate[1])
    selenium.select "date_of_exit_3[M]",exitDate1 

    exitDate2 =  convertObj.convertString(exitDate[0])
    selenium.select "date_of_exit_3[Y]", exitDate2

    selenium.type "location_3[1][address][city]", otherSchoolInfo[41].text
    selenium.select "location_3[1][address][state_province_id]", otherSchoolInfo[42].text
    selenium.select "location_3[1][address][country_id]", otherSchoolInfo[45].text
    selenium.type "note_3",otherSchoolInfo[39].text 


    #Other school information 4
    selenium.type "organization_name_4", otherSchoolInfo[48].text

    entryDate  = convertObj.convertDate(otherSchoolInfo[53].text)
    entryDate1 = convertObj.convertString(entryDate[1])
    selenium.select "date_of_entry_4[M]", entryDate1 

    entryDate2 = convertObj.convertString(entryDate[0])
    selenium.select "date_of_entry_4[Y]", entryDate2

    exitDate  = convertObj.convertDate(otherSchoolInfo[54].text)
    exitDate1 =  convertObj.convertString(exitDate[1])
    selenium.select "date_of_exit_4[M]",exitDate1 

    exitDate2 =  convertObj.convertString(exitDate[0])
    selenium.select "date_of_exit_4[Y]", exitDate2

    selenium.type "location_4[1][address][city]", otherSchoolInfo[57].text
    selenium.select "location_4[1][address][state_province_id]", otherSchoolInfo[58].text
    selenium.select "location_4[1][address][country_id]", otherSchoolInfo[61].text
    selenium.type "note_2",otherSchoolInfo[55].text 

    #Other school information 5
    selenium.type "organization_name_5", otherSchoolInfo[64].text

    entryDate  = convertObj.convertDate(otherSchoolInfo[69].text)
    entryDate1 = convertObj.convertString(entryDate[1])
    selenium.select "date_of_entry_5[M]", entryDate1 

    entryDate2 = convertObj.convertString(entryDate[0])
    selenium.select "date_of_entry_5[Y]", entryDate2

    exitDate  = convertObj.convertDate(otherSchoolInfo[70].text)
    exitDate1 =  convertObj.convertString(exitDate[1])
    selenium.select "date_of_exit_5[M]",exitDate1 

    exitDate2 =  convertObj.convertString(exitDate[0])
    selenium.select "date_of_exit_5[Y]", exitDate2

    selenium.type "location_5[1][address][city]", otherSchoolInfo[73].text
    selenium.select "location_5[1][address][state_province_id]", otherSchoolInfo[74].text
    selenium.select "location_5[1][address][country_id]", otherSchoolInfo[77].text
    selenium.type "note_5",otherSchoolInfo[71].text 
  end
end

#class to add Academic Information
class CPSAcademicInformation 
  def academicDetails(selenium, academicDetails, doc)
    convertObj = CPSConvert.new
    gpa = convertObj.convertString(academicDetails[0].text)
    selenium.select "gpa_unweighted_id", gpa

    gpa1 = convertObj.convertString(academicDetails[1].text)
    selenium.select "gpa_weighted_id", gpa1

    
    if academicDetails[0].text == "1" then
      selenium.type "class_rank", academicDetails[4].text 
      selenium.type "class_num_students", academicDetails[5].text      
    else
      selenium.click "document.Academic.is_class_ranking[1]"
    end
    
    selenium.select "class_rank_percent_id", academicDetails[3].text

    str = convertObj.checkString(academicDetails[6].text) 
    if ! str then
      selenium.type "gpa_explanation", academicDetails[6].text
    end

    #if selenium.click "is_alternate_grading" then
    #  selenium.type "alternate_grading_explanation", "If your school uses an alternate grading system, please explain the system."
    #else
    #  selenium.click "document.Academic.is_alternate_grading[1]"
    #end

    #honors 1
    selenium.type "description_1", academicDetails[7].text

    award_date = convertObj.convertDate(academicDetails[8].text)
    award_date1 = convertObj.convertString(award_date[1])
    selenium.select "award_date_1[M]", award_date1

    award_date2 = convertObj.convertString(award_date[0])
    selenium.select "award_date_1[Y]", award_date2

    #honors 2
    selenium.click "honor_2_show"
    
    selenium.type "description_2",academicDetails[9].text 
    
    award_date = convertObj.convertDate(academicDetails[10].text)
    award_date1 = convertObj.convertString(award_date[1])
    selenium.select "award_date_2[M]", award_date1
    
    award_date2 = convertObj.convertString(award_date[0])
    selenium.select "award_date_2[Y]", award_date2
    #honors 3
    selenium.click "honor_3_show"
    selenium.type "description_3", academicDetails[11].text
    
    award_date = convertObj.convertDate(academicDetails[12].text)
    award_date1 = convertObj.convertString(award_date[1])
    selenium.select "award_date_3[M]", award_date1
    
    award_date1 = convertObj.convertString(award_date[0])
    selenium.select "award_date_3[Y]", award_date2
    
    #honors 4
    selenium.click "honor_4_show"
    selenium.type "description_4", academicDetails[13].text
    
    award_date = convertObj.convertDate(academicDetails[14].text)
    award_date1 = convertObj.convertString(award_date[1])
    selenium.select "award_date_4[M]", award_date1

    award_date2 = convertObj.convertString(award_date[0])
    selenium.select "award_date_4[Y]", award_date2
        
    #honors 5
    selenium.click "honor_5_show"
    selenium.type "description_5", academicDetails[15].text

    award_date = convertObj.convertDate(academicDetails[16].text)
    award_date1 = convertObj.convertString(award_date[1])
    selenium.select "award_date_5[M]", award_date1

    award_date2 = convertObj.convertString(award_date[0])
    selenium.select "award_date_5[Y]", award_date2
    
    #honors 6
    selenium.type "description_6", academicDetails[17].text

    award_date = convertObj.convertDate(academicDetails[18].text)
    award_date1 = convertObj.convertString(award_date[1])
    selenium.select "award_date_6[M]", "label=Jun"

    award_date2 = convertObj.convertString(award_date[0])
    selenium.select "award_date_6[Y]", award_date2
  end
end

#class to add Grade 9 Information
class CPSGrade9Information 
  def grade9Details(selenium,grade9Details,doc)

    selenium.select "term_system_id", "label=Full (one final grade per year)"

    #Academic Subjects1
    
    selenium.select "academic_subject_id_1", "label=History / Social Science"
    selenium.type "course_title_1", "title 1"
    selenium.select "academic_credit_1", "label=0.50"
    selenium.select "academic_honor_status_id_1", "label=HL"
    selenium.select "grade_1_1", "label=A+"
    selenium.select "grade_1_2", "label=A+"
    selenium.select "grade_1_3", "label=A+"
    selenium.select "grade_1_4", "label=A+"
    
    #Academic Subjects2
    selenium.select "academic_subject_id_2", "label=English (Language of Instruction)"
    selenium.type "course_title_2", "title2"
    selenium.select "academic_credit_2", "label=2.50"
    selenium.select "academic_honor_status_id_2", "label=CL"
    selenium.select "grade_2_1", "label=A"
    selenium.select "grade_2_2", "label=A"
    selenium.select "grade_2_3", "label=A"
    selenium.select "grade_2_4", "label=A"

    #Academic Subjects2
    selenium.select "academic_subject_id_3", "label=English (Language of Instruction)"
    selenium.type "course_title_3", "title2"
    selenium.select "academic_credit_3", "label=2.50"
    selenium.select "academic_honor_status_id_3", "label=CL"
    selenium.select "grade_3_1", "label=A"
    selenium.select "grade_3_2", "label=A"
    selenium.select "grade_3_3", "label=A"
    selenium.select "grade_3_4", "label=A"

    #Academic Subjects2
    selenium.select "academic_subject_id_4", "label=English (Language of Instruction)"
    selenium.type "course_title_4", "title2"
    selenium.select "academic_credit_4", "label=2.50"
    selenium.select "academic_honor_status_id_4", "label=CL"
    selenium.select "grade_4_1", "label=A"
    selenium.select "grade_4_2", "label=A"
    selenium.select "grade_4_3", "label=A"
    selenium.select "grade_4_4", "label=A"
    
    #Academic Subjects12
    selenium.select "academic_subject_id_5", "label=English (Language of Instruction)"
    selenium.type "course_title_5", "title2"
    selenium.select "academic_credit_5", "label=2.50"
    selenium.select "academic_honor_status_id_5", "label=CL"
    selenium.select "grade_5_1", "label=A"
    selenium.select "grade_5_2", "label=A"
    selenium.select "grade_5_3", "label=A"
    selenium.select "grade_5_4", "label=A"
    
  end
end
    

#class to add Grade 10 Information
class CPSGrade10Information 
  def grade10Details (selenium,grade10Details, doc)
    convertObj = CPSConvert.new
    
    selenium.select "term_system_id", "label=Semester (two final grades per year)"
    
    subject = convertObj.convertString(grade10Details[7].text)
    selenium.select "academic_subject_id_1", subject
    selenium.type "course_title_1", grade10Details[0].text

    credit = convertObj.convertString(grade10Details[1].text)
    selenium.select "academic_credit_1", credit

    honor_status = convertObj.convertString(grade10Details[2].text)
    selenium.select "academic_honor_status_id_1",honor_status
    
    grade_1_1 = convertObj.convertString(grade10Details[3].text)
    selenium.select "grade_1_1", grade_1_1

    grade_1_2 = convertObj.convertString(grade10Details[4].text)
    selenium.select "grade_1_2", grade_1_2

    if grade10Details[5].text then
      grade_1_3 = convertObj.convertString(grade10Details[5].text)
      selenium.select "grade_1_3", grade_1_3
    end
    grade_1_4 = convertObj.convertString(grade10Details[6].text)
    selenium.select "grade_1_4", grade_1_4
    
    subject = convertObj.convertString(grade10Details[15].text)
    selenium.select "academic_subject_id_2", subject

    selenium.type "course_title_2",grade10Details[8].text

    credit = convertObj.convertString(grade10Details[9].text)
    selenium.select "academic_credit_2", credit

    honor_status = convertObj.convertString(grade10Details[10].text)
    selenium.select "academic_honor_status_id_2", honor_status

    grade_2_1 = convertObj.convertString(grade10Details[11].text)
    selenium.select "grade_2_1", grade_2_1

    grade_2_2 = convertObj.convertString(grade10Details[12].text)
    selenium.select "grade_2_2", grade_2_2

    grade_2_3 = convertObj.convertString(grade10Details[13].text)
    selenium.select "grade_2_3", grade_2_3

    grade_2_4 = convertObj.convertString(grade10Details[14].text)
    selenium.select "grade_2_4", grade_2_4

    subject = convertObj.convertString(grade10Details[23].text)
    selenium.select "academic_subject_id_3", subject

    selenium.type "course_title_3", grade10Details[16].text

    credit = convertObj.convertString(grade10Details[17].text)
    selenium.select "academic_credit_3", credit
    
    honor_status = convertObj.convertString(grade10Details[18].text)
    selenium.select "academic_honor_status_id_3", honor_status
    
    grade_3_1 = convertObj.convertString(grade10Details[19].text)
    selenium.select "grade_3_1", grade_3_1

    grade_3_2 = convertObj.convertString(grade10Details[20].text)
    selenium.select "grade_3_2", grade_3_2

    grade_3_3 = convertObj.convertString(grade10Details[21].text)
    selenium.select "grade_3_3", grade_3_3

    grade_3_4 = convertObj.convertString(grade10Details[22].text)
    selenium.select "grade_3_4", grade_3_4

    subject = convertObj.convertString(grade10Details[31].text)
    selenium.select "academic_subject_id_4", subject

    selenium.type "course_title_4", grade10Details[24].text

    credit = convertObj.convertString(grade10Details[25].text)
    selenium.select "academic_credit_4", credit

    honor_status = convertObj.convertString(grade10Details[26].text)
    selenium.select "academic_honor_status_id_4", honor_status

    grade_4_1 = convertObj.convertString(grade10Details[27].text)
    selenium.select "grade_4_1", grade_4_1

    grade_4_2 = convertObj.convertString(grade10Details[28].text)
    selenium.select "grade_4_2", grade_4_2

    grade_4_3 = convertObj.convertString(grade10Details[29].text)
    selenium.select "grade_4_3", grade_4_3

    grade_4_4 = convertObj.convertString(grade10Details[30].text)
    selenium.select "grade_4_4", grade_4_4

    subject = convertObj.convertString(grade10Details[39].text)
    selenium.select "academic_subject_id_5", subject

    selenium.type "course_title_5", grade10Details[32].text

    credit = convertObj.convertString(grade10Details[33].text)
    selenium.select "academic_credit_5", credit

    honor_status = convertObj.convertString(grade10Details[34].text)
    selenium.select "academic_honor_status_id_5", honor_status

    grade_5_1 = convertObj.convertString(grade10Details[35].text)
    selenium.select "grade_5_1", "label=B"

    grade_5_2 = convertObj.convertString(grade10Details[36].text)
    selenium.select "grade_5_2", "label=B+"

    grade_5_3 = convertObj.convertString(grade10Details[37].text)
    selenium.select "grade_5_3", "label=B"

    grade_5_4 = convertObj.convertString(grade10Details[38].text)
    selenium.select "grade_5_4", "label=B"

    subject = convertObj.convertString(grade10Details[47].text)
    selenium.select "academic_subject_id_6", subject

    selenium.type "course_title_6", grade10Details[40].text

    credit = convertObj.convertString(grade10Details[41].text)
    selenium.select "academic_credit_6", credit

    honor_status = convertObj.convertString(grade10Details[42].text)
    selenium.select "academic_honor_status_id_6", honor_status

    grade_6_1 = convertObj.convertString(grade10Details[43].text)
    selenium.select "grade_6_1", grade_6_1

    grade_6_2 = convertObj.convertString(grade10Details[44].text)
    selenium.select "grade_6_2", grade_6_2

    grade_6_3 = convertObj.convertString(grade10Details[45].text)
    selenium.select "grade_6_3", grade_6_3

    grade_6_4 = convertObj.convertString(grade10Details[46].text)
    selenium.select "grade_6_4", grade_6_4

    subject = convertObj.convertString(grade10Details[54].text)
    selenium.select "academic_subject_id_7", subject

    selenium.type "course_title_7", grade10Details[48].text

    credit = convertObj.convertString(grade10Details[49].text)
    selenium.select "academic_credit_7", credit

    honor_status = convertObj.convertString(grade10Details[50].text)
    selenium.select "academic_honor_status_id_7", honor_status

    grade_7_1 = convertObj.convertString(grade10Details[51].text)
    selenium.select "grade_7_1", "label=B+"

    grade_7_2 = convertObj.convertString(grade10Details[53].text)
    selenium.select "grade_7_2", "label=B-"

    grade_7_3 = convertObj.convertString(grade10Details[52].text)
    selenium.select "grade_7_3", "label=C+"

    grade_7_4 = convertObj.convertString(grade10Details[53].text)
    selenium.select "grade_7_4", "label=C"

    subject = convertObj.convertString(grade10Details[62].text)
    selenium.select "academic_subject_id_8", subject

    selenium.type "course_title_8", grade10Details[55].text

    credit = convertObj.convertString(grade10Details[56].text)
    selenium.select "academic_credit_8", credit

    honor_status = convertObj.convertString(grade10Details[57].text)
    selenium.select "academic_honor_status_id_8", honor_status

    grade_8_1 = convertObj.convertString(grade10Details[58].text)
    selenium.select "grade_8_1", grade_8_1

    grade_8_2 = convertObj.convertString(grade10Details[59].text)
    selenium.select "grade_8_2", grade_8_2

    grade_8_3 = convertObj.convertString(grade10Details[60].text)
    selenium.select "grade_8_3", grade_8_3

    grade_8_4 = convertObj.convertString(grade10Details[61].text)
    selenium.select "grade_8_4", grade_8_9

    subject = convertObj.convertString(grade10Details[70].text)
    selenium.select "academic_subject_id_9", subject

    selenium.type "course_title_9", grade10Details[63].text

    credit = convertObj.convertString(grade10Details[64].text)
    selenium.select "academic_credit_9", credit

    honor_status = convertObj.convertString(grade10Details[65].text)
    selenium.select "academic_honor_status_id_9", honor_status

    grade_8_4 = convertObj.convertString(grade10Details[66].text)
    selenium.select "grade_9_1", "label=C-"

    grade_8_4 = convertObj.convertString(grade10Details[67].text)
    selenium.select "grade_9_2", "label=D+"

    grade_8_4 = convertObj.convertString(grade10Details[68].text)
    selenium.select "grade_9_3", "label=D+"

    grade_8_4 = convertObj.convertString(grade10Details[69].text)
    selenium.select "grade_9_4", "label=D+"
    
    subject = convertObj.convertString(grade10Details[78].text)
    selenium.select "academic_subject_id_10", subject

    selenium.type "course_title_10", grade10Details[71].text

    credit = convertObj.convertString(grade10Details[72].text)
    selenium.select "academic_credit_10", credit

    honor_status = convertObj.convertString(grade10Details[73].text)
    selenium.select "academic_honor_status_id_10", honor_status

    grade_10_1 = convertObj.convertString(grade10Details[74].text)
    selenium.select "grade_10_1", grade_10_1

    grade_10_2 = convertObj.convertString(grade10Details[75].text)
    selenium.select "grade_10_2", grade_10_2

    grade_10_3 = convertObj.convertString(grade10Details[76].text)
    selenium.select "grade_10_3", grade_10_3

    grade_10_4 = convertObj.convertString(grade10Details[77].text)
    selenium.select "grade_10_4", grade_10_4

    subject = convertObj.convertString(grade10Details[86].text)
    selenium.select "academic_subject_id_11", subject

    selenium.type "course_title_11", grade10Details[79].text

    credit = convertObj.convertString(grade10Details[80].text)
    selenium.select "academic_credit_11", credit

    honor_status = convertObj.convertString(grade10Details[81].text)
    selenium.select "academic_honor_status_id_11", honor_status

    grade_11_1= convertObj.convertString(grade10Details[82].text)
    selenium.select "grade_11_1", grade_11_1

    grade_11_2= convertObj.convertString(grade10Details[83].text)
    selenium.select "grade_11_2", grade_11_2

    grade_11_3 = convertObj.convertString(grade10Details[84].text)
    selenium.select "grade_11_3", grade_11_3

    grade_11_4 = convertObj.convertString(grade10Details[85].text)
    selenium.select "grade_11_4", grade_11_4
    
    subject = convertObj.convertString(grade10Details[94].text)
    selenium.select "academic_subject_id_12", subject

    selenium.type "course_title_12", grade10Details[87].text

    honor_status = convertObj.convertString(grade10Details[88].text)
    selenium.select "academic_honor_status_id_12", honor_status

    credit = convertObj.convertString(grade10Details[89].text)
    selenium.select "academic_credit_12", credit

    grade_12_1= convertObj.convertString(grade10Details[90].text)
    selenium.select "grade_12_1", grade_12_1

    grade_12_2 = convertObj.convertString(grade10Details[91].text)
    selenium.select "grade_12_2", grade_12_2

    grade_12_3 = convertObj.convertString(grade10Details[92].text)
    selenium.select "grade_12_3", grade_12_3

    grade_12_4 = convertObj.convertString(grade10Details[93].text)
    selenium.select "grade_12_4", grade_12_4
  end
end


#class to add Grade 10 Information
class CPSGrade12Information 
  def grade12Details (selenium,grade12Details, doc)
    convertObj = CPSConvert.new
    
    selenium.select "term_system_id", "label=Semester (two final grades per year)"
    
    subject = convertObj.convertString(grade12Details[7].text)
    selenium.select "academic_subject_id_1", subject
    selenium.type "course_title_1", grade12Details[0].text

    credit = convertObj.convertString(grade12Details[1].text)
    selenium.select "academic_credit_1", credit

    honor_status = convertObj.convertString(grade12Details[2].text)
    selenium.select "academic_honor_status_id_1",honor_status
    
    subject = convertObj.convertString(grade12Details[15].text)
    selenium.select "academic_subject_id_2", subject

    selenium.type "course_title_2",grade12Details[8].text

    credit = convertObj.convertString(grade12Details[9].text)
    selenium.select "academic_credit_2", credit

    honor_status = convertObj.convertString(grade12Details[10].text)
    selenium.select "academic_honor_status_id_2", honor_status

    subject = convertObj.convertString(grade12Details[23].text)
    selenium.select "academic_subject_id_3", subject

    selenium.type "course_title_3", grade12Details[16].text

    credit = convertObj.convertString(grade12Details[17].text)
    selenium.select "academic_credit_3", credit
    
    honor_status = convertObj.convertString(grade12Details[18].text)
    selenium.select "academic_honor_status_id_3", honor_status

    subject = convertObj.convertString(grade12Details[31].text)
    selenium.select "academic_subject_id_4", subject

    selenium.type "course_title_4", grade12Details[24].text

    credit = convertObj.convertString(grade12Details[25].text)
    selenium.select "academic_credit_4", credit

    honor_status = convertObj.convertString(grade12Details[26].text)
    selenium.select "academic_honor_status_id_4", honor_status

    subject = convertObj.convertString(grade12Details[39].text)
    selenium.select "academic_subject_id_5", subject

    selenium.type "course_title_5", grade12Details[32].text

    credit = convertObj.convertString(grade12Details[33].text)
    selenium.select "academic_credit_5", credit

    honor_status = convertObj.convertString(grade12Details[34].text)
    selenium.select "academic_honor_status_id_5", honor_status

    subject = convertObj.convertString(grade12Details[47].text)
    selenium.select "academic_subject_id_6", subject

    selenium.type "course_title_6", grade12Details[40].text

    credit = convertObj.convertString(grade12Details[41].text)
    selenium.select "academic_credit_6", credit

    honor_status = convertObj.convertString(grade12Details[42].text)
    selenium.select "academic_honor_status_id_6", honor_status

    subject = convertObj.convertString(grade12Details[54].text)
    selenium.select "academic_subject_id_7", subject

    selenium.type "course_title_7", grade12Details[48].text

    credit = convertObj.convertString(grade12Details[49].text)
    selenium.select "academic_credit_7", credit

    honor_status = convertObj.convertString(grade12Details[50].text)
    selenium.select "academic_honor_status_id_7", honor_status

    subject = convertObj.convertString(grade12Details[62].text)
    selenium.select "academic_subject_id_8", subject

    selenium.type "course_title_8", grade12Details[55].text

    credit = convertObj.convertString(grade12Details[56].text)
    selenium.select "academic_credit_8", credit

    honor_status = convertObj.convertString(grade12Details[57].text)
    selenium.select "academic_honor_status_id_8", honor_status

    subject = convertObj.convertString(grade12Details[70].text)
    selenium.select "academic_subject_id_9", subject

    selenium.type "course_title_9", grade12Details[63].text

    credit = convertObj.convertString(grade12Details[64].text)
    selenium.select "academic_credit_9", credit

    honor_status = convertObj.convertString(grade12Details[65].text)
    selenium.select "academic_honor_status_id_9", honor_status

    subject = convertObj.convertString(grade12Details[78].text)
    selenium.select "academic_subject_id_10", subject

    selenium.type "course_title_10", grade12Details[71].text

    credit = convertObj.convertString(grade12Details[72].text)
    selenium.select "academic_credit_10", credit

    honor_status = convertObj.convertString(grade12Details[73].text)
    selenium.select "academic_honor_status_id_10", honor_status

    subject = convertObj.convertString(grade12Details[86].text)
    selenium.select "academic_subject_id_11", subject

    selenium.type "course_title_11", grade12Details[79].text

    credit = convertObj.convertString(grade12Details[80].text)
    selenium.select "academic_credit_11", credit

    honor_status = convertObj.convertString(grade12Details[81].text)
    selenium.select "academic_honor_status_id_11", honor_status

    subject = convertObj.convertString(grade12Details[94].text)
    selenium.select "academic_subject_id_12", subject

    selenium.type "course_title_12", grade12Details[87].text

    honor_status = convertObj.convertString(grade12Details[88].text)
    selenium.select "academic_honor_status_id_12", honor_status

    credit = convertObj.convertString(grade12Details[89].text)
    selenium.select "academic_credit_12", credit

  end
end

#class to add Testing Information
class CPSTestingInformation 
  def testingDetails(selenium,doc)
    #Read Date Information for different Testing
    convertObj = CPSConvert.new()
   
    actScores1     = Array.new()
    actScores2     = Array.new()
    actScores3     = Array.new()
    satScores1     = Array.new() 
    satScores2     = Array.new()
    satIIScores1   = Array.new()
    satIIScores2   = Array.new()
    satIIScores3   = Array.new()
    satIIScores4   = Array.new()
    satIIScores5   = Array.new()
    apScores1      = Array.new()
    apScores2      = Array.new()
    apScores3      = Array.new()
    apScores4      = Array.new()
    apScores5      = Array.new()
    apScores6      = Array.new()
    apScores7      = Array.new()
    apScores8      = Array.new()
    toeflScores1   = Array.new()
    toeflScores2   = Array.new()
    toeflScores3   = Array.new()
    psatScores     = Array.new()
    
    #if @doc.elements["StudentDetail/test_ACT_1"].has_elements? then
    doc.get_elements("StudentDetail/test_ACT_1").collect{|test1| 
      test1.each_element{|a1| 
        actScores1 = actScores1.push(a1)
      }
    }
    doc.get_elements("StudentDetail/test_ACT_2").collect{|test2| 
      test2.each_element{|a2|
        actScores2 = actScores2.push(a2)
      }
    } 
      doc.get_elements("StudentDetail/test_ACT_3").collect{|test3| 
      test3.each_element{|a3| 
        actScores3 = actScores3.push(a3)
      }
    }    
    doc.get_elements("StudentDetail/test_SAT_1").collect{|test4| 
      test4.each_element{|a4| 
        satScores1 = satScores1.push(a4)
      }
    }
    doc.get_elements("StudentDetail/test_SAT_2").collect{|test5| 
      test5.each_element{|a5| 
        satScores2 = satScores2.push(a5)
      }
    }
    doc.get_elements("StudentDetail/test_SAT_II_1").collect{|test6| 
      test6.each_element{|a6| 
        satIIScores1 = satIIScores1.push(a6)
      }
    }
    doc.get_elements("StudentDetail/test_SAT_II_2").collect{|test7| 
      test7.each_element{|a7| 
        satIIScores2 = satIIScores2.push(a7)
      }
    }
    doc.get_elements("StudentDetail/test_SAT_II_3").collect{|test8| 
      test8.each_element{|a8| 
        satIIScores3 = satIIScores3.push(a8)
      }
    }
    doc.get_elements("StudentDetail/test_SAT_II_4").collect{|test9| 
      test9.each_element{|a9| 
        satIIScores4 = satIIScores4.push(a9)
      }
    }
    doc.get_elements("StudentDetail/test_SAT_II_5").collect{|test10| 
      test10.each_element{|a10| 
        satIIScores5 = satIIScores5.push(a10)
      }
      }
    doc.get_elements("StudentDetail/test_AP_1").collect{|test11| 
      test11.each_element{|a11| 
        apScores1 = apScores1.push(a11)
      }
    }
    doc.get_elements("StudentDetail/test_AP_2").collect{|test12| 
      test12.each_element{|a12| 
      apScores2 = apScores2.push(a12)
      }
    }
    doc.get_elements("StudentDetail/test_AP_3").collect{|test13| 
      test13.each_element{|a13| 
        apScores3 = apScores3.push(a13)
      }
    }
    doc.get_elements("StudentDetail/test_AP_4").collect{|test14| 
      test14.each_element{|a14| 
        apScores4 = apScores4.push(a14)
      }
    }
    doc.get_elements("StudentDetail/test_AP_5").collect{|test15| 
      test15.each_element{|a15| 
        apScores5 = apScores5.push(a15)
      }
    }
    doc.get_elements("StudentDetail/test_AP_6").collect{|test16| 
      test16.each_element{|a16| 
        apScores6 = apScores6.push(a16)
      }
    }
    doc.get_elements("StudentDetail/test_AP_7").collect{|test17| 
      test17.each_element{|a17| 
        apScores7 = apScores7.push(a17)
      }
      }
      doc.get_elements("StudentDetail/test_AP_8").collect{|test18| 
      test18.each_element{|a18| 
      apScores8 = apScores8.push(a18)
      }
    }
    doc.get_elements("StudentDetail/test_TOEFL_1").collect{|test19| 
      test19.each_element{|a19| 
        toeflScores1 = toeflScores1.push(a19)
      }
    }
    doc.get_elements("StudentDetail/test_TOEFL_2").collect{|test20| 
      test20.each_element{|a20| 
        toeflScores2 = toeflScores2.push(a20)
      }
    }
    doc.get_elements("StudentDetail/test_TOEFL_3").collect{|test21| 
      test21.each_element{|a21| 
        toeflScores3 = toeflScores3.push(a21)
      }
    }
    doc.get_elements("StudentDetail/test_PSAT_1").collect{|test22| 
      test22.each_element{|a22| 
        psatScores = psatScores.push(a22)
      }
    }
    if ! actScores1.empty? then
      #enter ACT Scores
      #get ACT Date
      act1_date    = convertObj.convertDate(actScores1[8].text)
      
      selenium.type "act_english_1", actScores1[4].text
      selenium.type "act_reading_1", actScores1[5].text
      selenium.type "act_math_1",    actScores1[6].text
      selenium.type "act_science_1", actScores1[7].text
      
      mon_date = convertObj.convertString(act1_date[1])
      selenium.select "act_date_1[M]", mon_date
      
      year_date = convertObj.convertString(act1_date[0])
      selenium.select "act_date_1[Y]", year_date
    end

    #Add another ACT test score
    if ! actScores2.empty? then
     #get ACT Date
      act2_date    = convertObj.convertDate(actScores2[8].text)
      
      selenium.type "act_english_2", actScores2[4].text
      selenium.type "act_reading_2", actScores2[5].text
      selenium.type "act_math_2",    actScores2[6].text
      selenium.type "act_science_2", actScores2[7].text
      
      mon_date = convertObj.convertString(act2_date[1])
      selenium.select "act_date_2[M]", mon_date
      
      year_date = convertObj.convertString(act2_date[0])
      selenium.select "act_date_2[Y]", year_date      
    end

    if ! actScores3.empty? then
      #Add another ACT test score      
      
      #get ACT Date
      act3_date    = convertObj.convertDate(actScores3[8].text)
      
      selenium.type "act_english_3",  actScores3[4].text
      selenium.type "act_reading_3",  actScores3[5].text
      selenium.type "act_math_3",     actScores3[6].text
      selenium.type "act_science_3",  actScores3[7].text
      
      mon_date = convertObj.convertString(act3_date[1])
      selenium.select "act_date_3[M]", mon_date
      
      year_date = convertObj.convertString(act3_date[0])
      selenium.select "act_date_3[Y]", year_date
    end
      
      if ! satScores1.empty? then
        #enter SAT1 Scores
      
      #get SAT Date
      sat1_date    = convertObj.convertDate(satScores1[8].text)
      
      selenium.type "sat_criticalreading_1",satScores1[5].text
      selenium.type "sat_math_1",           satScores1[6].text
      selenium.type "sat_writing_1",        satScores1[7].text
      
      mon_date = convertObj.convertString(sat1_date[1])
      selenium.select "sat_date_1[M]", mon_date
      
      year_date = convertObj.convertString(sat1_date[0])
      selenium.select "sat_date_1[Y]", year_date
    end
      
    if ! satScores2.empty? then
      #Enter SAT2 Scores
      sat2_date    = convertObj.convertDate(satScores2[8].text)
      
      selenium.type "sat_criticalreading_2", satScores2[5].text
      selenium.type "sat_math_2",            satScores2[6].text
      selenium.type "sat_writing_2",         satScores2[7].text
      
      mon_date = convertObj.convertString(sat2_date[1])
      selenium.select "sat_date_2[M]", mon_date
      
      year_date = convertObj.convertString(sat2_date[0])
      selenium.select "sat_date_2[Y]", year_date
    end

    if ! psatScores.empty? then
      #enter PSAT Scores
      
      #get PSAT Date
      psat_date    = convertObj.convertDate(psatScores[8].text)
      
      selenium.type "psat_criticalreading_1", psatScores[5].text
      selenium.type "psat_math_1",            psatScores[6].text
      selenium.type "psat_writing_1",         psatScores[7].text
      
      mon_date = convertObj.convertString(psat_date[1])
      selenium.select "psat_date_1[M]", mon_date
      
      year_date = convertObj.convertString(psat_date[0])
      selenium.select "psat_date_1[Y]", year_date
    end
      
      if ! satIIScores1.empty? then
    #enter SATII-1 Scores
    #get SATII Date
    satII1_date  = convertObj.convertDate(satIIScores1[4].text)


    satII_sub  = convertObj.convertString(satIIScores1[5].text)
    satII_score = convertObj.convertString(satIIScores1[3].text)

    selenium.select "satII_subject_id_1",satII_sub
    selenium.type "satII_score_1", satII_score      

    mon_date = convertObj.convertString(satII1_date[1])
    selenium.select "satII_date_1[M]", mon_date

    year_date = convertObj.convertString(satII1_date[0])
    selenium.select "satII_date_1[Y]", year_date
    end

    if ! satIIScores2.empty? then
    #enter SATII-2 Scores
    #get SATII Date
    satII2_date  = convertObj.convertDate(satIIScores2[4].text)

    satII_sub  = convertObj.convertString(satIIScores2[5].text)
    satII_score = convertObj.convertString(satIIScores2[3].text)
   
    selenium.select "satII_subject_id_2",   satII_sub
    selenium.type "satII_score_2", satII_score       

    mon_date = convertObj.convertString(satII2_date[1])
    selenium.select "satII_date_2[M]", mon_date

    year_date = convertObj.convertString(satII2_date[0])
    selenium.select "satII_date_2[Y]", year_date
    end

    if ! satIIScores3.empty? then
    #enter SATII-3 Scores
    #get SATII Date
    satII3_date  = convertObj.convertDate(satIIScores3[4].text)

    satII_sub  = convertObj.convertString(satIIScores3[5].text)
    satII_score = convertObj.convertString(satIIScores3[3].text)

    selenium.select "satII_subject_id_3", satII_sub  
    selenium.type "satII_score_3", satII_score       

    mon_date = convertObj.convertString(satII3_date[1])
    selenium.select "satII_date_3[M]", mon_date

    year_date = convertObj.convertString(satII3_date[0])
    selenium.select "satII_date_3[Y]", year_date
    end

    if ! satIIScores4.empty? then
    #enter SATII-4 Scores
    #get SATII Date
    satII4_date  = convertObj.convertDate(satIIScores4[4].text)

    satII_sub  = convertObj.convertString(satIIScores4[5].text)
    selenium.select "satII_subject_id_4", satII_sub
    selenium.type "satII_score_4",        satIIScores4[3].text

    mon_date = convertObj.convertString(satII4_date[1])
    selenium.select "satII_date_4[M]", mon_date

    year_date = convertObj.convertString(satII4_date[0])
    selenium.select "satII_date_4[Y]", year_date 
    end

    if ! satIIScores5.empty? then                  
    #enter SATII-5 Scores
    #get SATII Date
    satII5_date  = convertObj.convertDate(satIIScores5[4].text)

    satII_sub  = convertObj.convertString(satIIScores5[5].text)
    selenium.select "satII_subject_id_5",   satII_sub

    satII_score  = convertObj.convertString(satIIScores5[3].text)
    selenium.type "satII_score_5",satII_score         

    mon_date = convertObj.convertString(satII5_date[1])
    selenium.select "satII_date_5[M]", mon_date

    year_date = convertObj.convertString(satII5_date[0])
    selenium.select "satII_date_5[Y]", year_date
    end

    if ! apScores1.empty? then

    #enter AP-1 Scores
    #get AP Date
    ap1_date     = convertObj.convertDate(apScores1[5].text)

    ap_sub = convertObj.convertString(apScores1[4].text)
    selenium.select "ap_subject_id_1",   ap_sub 
    
    ap_score = convertObj.convertString(apScores1[8].text)
    selenium.select "ap_score_id_1",   ap_score

    mon_date = convertObj.convertString(ap1_date[1])
    selenium.select "ap_date_1[M]", mon_date

    year_date = convertObj.convertString(ap1_date[0])
    selenium.select "ap_date_1[Y]", year_date
    end

    if ! apScores2.empty? then  

    #enter AP-2 Scores
    ap2_date     = convertObj.convertDate(apScores2[5].text)

    satII_sub  = convertObj.convertString(apScores2[4].text)
    selenium.select "ap_subject_id_2", satII_sub

    satII_score  = convertObj.convertString(apScores2[8].text)
    selenium.select "ap_score_id_2", satII_score

    mon_date = convertObj.convertString(ap2_date[1])
    selenium.select "ap_date_2[M]", mon_date

    year_date = convertObj.convertString(ap2_date[0])
    selenium.select "ap_date_2[Y]", year_date
    end
    if ! apScores3.empty? then
    #enter AP-3 Scores
    ap3_date     = convertObj.convertDate(apScores3[5].text)

    ap_sub  = convertObj.convertString(apScores3[4].text)
    ap_score = convertObj.convertString(apScores3[8].text)

    selenium.select "ap_subject_id_3", ap_sub
    selenium.select "ap_score_id_3", ap_score

    mon_date = convertObj.convertString(ap3_date[1])
    selenium.select "ap_date_3[M]", mon_date

    year_date = convertObj.convertString(ap3_date[0])
    selenium.select "ap_date_3[Y]", year_date
    end
    if ! apScores4.empty? then
    #enter AP-4 Scores
    ap4_date     = convertObj.covertDate(apScores4[5].text)

    ap_sub = convertObj.convertString(apScores4[4].text)
    selenium.select "ap_subject_id_4",  ap_sub

    ap_score = convertObj.convertString(apScores4[8].text)
    selenium.select "ap_score_id_4",   ap_score  

    mon_date = convertObj.convertString(ap4_date[1])
    selenium.select "ap_date_4[M]", mon_date

    year_date = convertObj.convertString(ap3_date[0])
    selenium.select "ap_date_4[Y]", year_date
    end

    if ! apScores5.empty? then
    #enter AP-5 Scores
    ap5_date     = convertObj.convertDate(apScores5[5].text)

    ap_sub  = convertObj.convertString(apScores5[4].text)
    ap_score = convertObj.convertString(apScores5[8].text)

    selenium.select "ap_subject_id_5", ap_sub  #"label=Computer Science A" 
    selenium.select "ap_score_id_5", ap_score   #"label=3"

    mon_date = convertObj.convertString(ap5_date[1])
    selenium.select "ap_date_5[M]", mon_date

    year_date = convertObj.convertString(ap5_date[0])
    selenium.select "ap_date_5[Y]", year_date
    end

    if ! apScores6.empty? then
    #enter AP-6 Scores
    ap6_date     = convertObj.convertDate(apScores6[5].text)

    ap_sub  = convertObj.convertString(apScores6[4].text)
    ap_score = convertObj.convertString(apScores6[8].text)

    selenium.select "ap_subject_id_6", ap_sub 
    selenium.select "ap_score_id_6",   ap_score

    mon_date = convertObj.convertString(ap6_date[1])
    selenium.select "ap_date_6[M]", mon_date

    year_date = convertObj.convertString(ap6_date[0])
    selenium.select "ap_date_6[Y]", year_date[0] 
    end

    if ! apScores7.empty? then
    #enter AP-7 Scores

    ap7_date     = convertObj.convertDate(apScores7[5].text)

    ap_sub  = convertObj.convertString(apScores7[4].text)
    ap_score = convertObj.convertString(apScores7[8].text)

    selenium.select "ap_subject_id_7", ap_sub 
    selenium.select "ap_score_id_7",   ap_score

    mon_date = convertObj.convertString(ap7_date[1])
    selenium.select "ap_date_7[M]", mon_date

    year_date = convertObj.convertString(ap7_date[0])
    selenium.select "ap_date_7[Y]", year_date
    end

    if ! apScores8.empty? then
    #enter AP-8 Scores
    ap8_date     = convertObj.convertDate(apScores8[5].text)
    ap_sub  = convertObj.convertString(apScores8[4].text)
    ap_score = convertObj.convertString(apScores8[8].text)
    selenium.select "ap_subject_id_8",   ap_sub
    selenium.select "ap_score_id_8",   ap_score

    mon_date = convertObj.convertString(ap8_date[1])
    selenium.select "ap_date_8[M]", mon_date

    year_date = convertObj.convertString(ap8_date[0])
    selenium.select "ap_date_8[Y]", year_date
    end

    if ! toeflScores1.empty? then
      #enter TOEFL-1 Scores
      #get TOEFL Date
      toefl1_date  = convertObj.convertDate(toeflScores1[5].text)
      
      toefl_score = convertObj.convertString(toeflScores1[4].text)
      selenium.type "toefl_score_1",toefl_score
      
      mon_date = convertObj.convertString(toefl1_date[1])
      selenium.select "toefl_date_1[M]", mon_date
      
      year_date = convertObj.convertString(toefl1_date[0])
      selenium.select "toefl_date_1[Y]", year_date
    end
      if ! toeflScores2.empty? then
        #enter TOEFL-2 Scores
        #get TOEFL Date
        toefl2_date  = convertObj.convertDate(toeflScores2[5].text)
        toefl_score = convertObj.convertString(toeflScores2[4].text)
        selenium.type "toefl_score_2", toefl_score
        
        mon_date = convertObj.convertString(toefl2_date[1])
        selenium.select "toefl_date_2[M]", mon_date
        
        year_date = convertObj.convertString(toefl2_date[0])
        selenium.select "toefl_date_2[Y]", year_date
      end
      
      if ! toeflScores3.empty? then
    #enter TOEFL-3 Scores
    #get TOEFL Date
    toefl3_date  = convertObj.convertDate(toeflScores3[5].text)
    
    toefl_score = convertObj.convertString(toeflScores3[5].text)
    selenium.type "toefl_score_3", toefl_score

    mon_date = convertObj.convertString(toefl3_date[1])
    selenium.select "toefl_date_3[M]", mon_date

    year_date = convertObj.convertString(toefl3_date[0])
    selenium.select "toefl_date_3[Y]", year_date
    end
    #Do you plan to take the SAT again?
    if selenium.is_checked('is_SAT_again') then
      #When do you plan to take the SAT?
      selenium.select "SAT_plan_date[M]", "label=Apr"
      selenium.select "SAT_plan_date[Y]", "label=2006"
    else
      selenium.check "document.Testing.is_SAT_again[1]"
    end

    #Do you plan to take the ACT again?
    if selenium.is_checked('is_ACT_again') then
      #When do you plan to take the ACT?
      selenium.select "ACT_plan_date[M]", "label=Aug"
      selenium.select "ACT_plan_date[Y]", "label=2006"
    else
      selenium.check "document.Testing.is_ACT_again[1]"
    end

    #Do you plan to take any more SAT II tests?
    if selenium.is_checked('is_more_SATII') then
      #Which subjects?
      selenium.type "more_SATII_subjects", "English"
      #When do you plan to take more SATIIs?
      selenium.select "SATII_plan_date[M]", "label=Jun"
      selenium.select "SATII_plan_date[Y]", "label=2007"
    else  
      selenium.check "document.Testing.is_more_SATII[1]"
    end
    
    #Have you received tutoring or taken test prep classes for any of the standardized tests?
    if selenium.is_checked('is_tutoring') then 
      selenium.check "is_tutoring"
    else
      selenium.check "document.Testing.is_tutoring[1]"
    end
  end
end


#class to add Biographical Essay Information
class CPSEssay
  def biographicalEssay(selenium, essayBiographical)

    #check if no Essay persent
    if essayBiographical.empty? then
      selenium.type "essay[biographical]", ""  
    else
      selenium.type "essay[biographical]", essayBiographical[0].text
    end
  end
  #function to add Optional Essay Information
  def optionalEssay(selenium, essayOptional)

    #check if no Essay persent
    if essayOptional.empty? then
      selenium.type "essay[optional]", ""  
    else
      selenium.type "essay[optional]", essayOptional[0].text
    end
  end
end

#class to add College Match Ranking Information
class CPSMatchRankingInformation 
  def matchRankingDetails(selenium, partner)

    convertObj = CPSConvert.new


    partner  = convertObj.convertString(partner[0].text)
    selenium.select "college_ranking_1", partner

    partner  = convertObj.convertString(partner[1].text)
    selenium.select "college_ranking_2", partner

    partner  = convertObj.convertString(partner[2].text)
    selenium.select "college_ranking_3", partner

    partner  = convertObj.convertString(partner[3].text)
    selenium.select "college_ranking_4", partner

    partner  = convertObj.convertString(partner[4].text)
    selenium.select "college_ranking_5", partner

    partner  = convertObj.convertString(partner[5].text)
    selenium.select "college_ranking_6", partner

    partner  = convertObj.convertString(partner[6].text)
    selenium.select "college_ranking_6", partner

    partner  = convertObj.convertString(partner[7].text)
    selenium.select "college_ranking_7", partner

    partner  = convertObj.convertString(partner[8].text)
    selenium.select "college_ranking_8", partner

    partner  = convertObj.convertString(partner[9].text)
    selenium.select "college_ranking_9", partner

    partner  = convertObj.convertString(partner[10].text)
    selenium.select "college_ranking_10",partner

    partner  = convertObj.convertString(partner[11].text)
    selenium.select "college_ranking_11",partner

    partner  = convertObj.convertString(partner[12].text)
    selenium.select "college_ranking_12",partner

    partner  = convertObj.convertString(partner[13].text)
    selenium.select "college_ranking_13",partner

    partner  = convertObj.convertString(partner[14].text)
    selenium.select "college_ranking_14",partner
       
    selenium.check "match_likely_id"
  end
end

#class to add Application Forwarding Information
class CPSAppForwardingInformation 
  def appForwardingDetails(selenium, partner)
    
    if partner[0].text == "1" then
      selenium.check "regular_addmission_1"  
    end
    
    if partner[1].text == "1" then
      selenium.check "regular_addmission_2"  
    end
    
    if partner[2].text == "1" then
      selenium.check "regular_addmission_3"
    end

    if partner[3].text == "1" then
      selenium.check "regular_addmission_4"    
    end

    if partner[4].text == "1" then
      selenium.check "regular_addmission_5"    
    end

    if partner[5].text == "1" then
      selenium.check "regular_addmission_6"
    end

    if partner[6].text == "1" then
      selenium.check "regular_addmission_7"
    end

    if partner[7].text == "1" then
      selenium.check "regular_addmission_8"
    end

    if partner[8].text == "1" then
      selenium.check "regular_addmission_9"
    end

    if partner[9].text == "1" then
      selenium.check "regular_addmission_10"
    end

    if partner[10].text == "1" then
      selenium.check "regular_addmission_11"
    end

    if partner[11].text == "1" then
      selenium.check "regular_addmission_12"
    end

    if partner[12].text == "1" then
      selenium.check "regular_addmission_13"
    end

    if partner[13].text == "1" then
      selenium.check "regular_addmission_14"
    end

    if partner[14].text == "1" then
      selenium.check "regular_addmission_15"
    end

    if partner[15].text == "1" then
      selenium.check "scholarship_addmission_16"
    end

    if partner[16].text == "1" then
      selenium.check "scholarship_addmission_17"
    end
    if partner[17].text == "1" then
      selenium.check "scholarship_addmission_18"
    end
    if partner[18].text == "1" then
      selenium.check "scholarship_addmission_19"
    end
  end
end

