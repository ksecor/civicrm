# This is a test case of using Selenium and its Ruby bindings
# Information Personal information definition 
# This test case allows you to add/edit personal information information

require 'cps_page_controller'
require '../selenium'
require 'cps_information'
require '/usr/lib/ruby/1.8/rexml/document'
include REXML

#require 'open3'

class TC_TestCPSPersonal < Test::Unit::TestCase
  def setup
    @page = CPSPageController.new
    @selenium = @page.start_civicrm

    #Retrieve information for an Individual
    @files = @page.openFileDirectory()
    @page.login
  end

  def teardown
    @page.logout
  end

  #Add Information in CPS Form   
  def test_cps
    @files.each {|file|
      #create a file Object
      @fileObject = File.new(file)

      #create an object to open a document
      @doc = Document.new(@fileObject)

      #move to CPS Form
      move_to_cps()
            
      #Personal Information Section
      move_to_personal_information(@doc)
      
      #Household Information Section
      move_to_household_information(@doc)
      
      #School Information Section
      move_to_school_information(@doc)
      
      #Tarsncript Section
      move_to_transcript(@doc)
      
      #Testing Section
      add_testing_information(@doc)
      
      #Essay Section
      move_to_essay(@doc)
      
      #College Match Ranking Section
      add_college_match_ranking(@doc)
      
      #Application Forwarding Section
      add_application_forwarding(@doc)
      
      #Save Draft
      save_draft()
      
      #Submit Application
      submit_application()
    }
  end

  def move_to_cps
    #Set URL http://localhost/drupal/civicrm/quest/cps
    @selenium.open "/drupal/civicrm/quest/cps"
  end

  #Add Personal Information
  def move_to_personal_information doc
    #Open a file for Individual Information

    add_personal_information(doc)
    add_additional_information(doc)
    add_educational_information(doc)
    # add_extraCurricular_information(doc)
    #add_workExperience_information(doc)
  end

  def move_to_household_information doc
    #Clicking Household Information
    assert_equal "Household Information", @selenium.get_text("link=Household Information")
    @page.click_and_wait "link=Household Information"

    add_household_information(doc)
   # add_income_information(doc)
   # add_sibling_information(doc)
  end

  def move_to_school_information doc
    #Clicking School Information
    assert_equal "School Information", @selenium.get_text("link=School Information")
    @page.click_and_wait "link=School Information"

    #add_highSchool_information(doc)
    add_otherSchool_information(doc)
    add_academic_information(doc)
  end

  def move_to_transcript doc
    #Clicking Transcript Information
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/school?_qf_Transcript-Nine_display=true"
    
    #add_grade9_transcript(doc)
    #add_grade10_transcript(doc)
    #add_grade11_transcript(doc)
    #add_grade12_transcript(doc)
    #add_summer_school(doc)
  end
  
  def move_to_essay doc
    #Clicking Essay Information
    assert_equal "Essays", @selenium.get_text("link=Essays")
    @page.click_and_wait "link=Essays"

    add_biographical_essay(doc)
    add_optional_essay(doc)
  end
    
  # Add new personal information
  def add_personal_information doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/personal?_qf_Personal_display=true"

    #create an array to store student personal information
    studentInfo = Array.new()
    
    studentInfo = getStudentInfo(doc)
    if ! studentInfo.empty? then
      #add details 
      @personal = CPSPersonalInformation.new
      @personal.personalDetails(@selenium, studentInfo,@doc)
    end
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Personal_upload']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Personal_upload']"
  end

  # Add additional information
  def add_additional_information doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/personal?_qf_Scholarship_display=true"
    
    additionalInfo = Array.new()

    # Retrieve Details
    @doc.get_elements("StudentDetail/Student").collect{|info|
      info.each_element{|additional| 
        additionalInfo = additionalInfo.push(additional)
      }
    }
    if ! additionalInfo.empty? then
      #add details 
      @additional = CPSAdditionalInformation.new
      @additional.additionalDetails(@selenium, additionalInfo)
    end
    #Submit the form 
    #if @page.click_and_wait "//input[@type='submit' and @name='_qf_Scholarship_back']" then
    #  @page.click_and_wait "//input[@type='submit' and @name='_qf_Scholarship_back']"
    #else
      assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Scholarship_next']")
      @page.click_and_wait "//input[@type='submit' and @name='_qf_Scholarship_next']"
    #end
  end

  # Add educational information
  def add_educational_information doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/personal?_qf_Educational_display=true"

    interestDetails = Array.new()
    @doc.get_elements("StudentDetail/Student/educational_interest_display").collect{|interest|
        interestDetails = interestDetails.push(interest)
    }
    @doc.get_elements("StudentDetail/Student/college_type_display").collect{|college|
        interestDetails = interestDetails.push(college)
    }
    if ! interestDetails.empty? then
      #add details 
      @educational = CPSEducationalInformation.new
      @educational.educationalDetails(@selenium, interestDetails, doc)
    end
   
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Educational_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Educational_next']"
  end

  # Add extraCurricular information
  def add_extraCurricular_information doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/personal?_qf_ExtracurricularInfo_display=true"

    curricularInfo = Array.new()
    curricularInfo = getExtracurricularInfo(doc)
    if ! curricularInfo.empty? then
      #add details 
      @extraCurricular = CPSExtracurricularInformation.new
      @extraCurricular.extraCurricularDetails(@selenium,curricularInfo, doc)
    end
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_ExtracurricularInfo_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_ExtracurricularInfo_next']"
  end

  # Add workExperience information
  def add_workExperience_information doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/personal?_qf_WorkExperience_display=true"
    
    workDetails =Array.new()
    workDetails = getWorkExperience(doc)
    if ! workDetails.empty? then
    #add details 
    @workExperience = CPSWorkExperienceInformation.new
    @workExperience.workExperienceDetails(@selenium,workDetails,doc)
    end
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_WorkExperience_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_WorkExperience_next']"

  end

  # Add household information
  def add_household_information doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/household?_qf_Household_display=true"

    householdDetails = Array.new()
    
    householdDetails = getHouseholdDetails(doc)

    if ! householdDetails.empty? then
      #add details 
      @household = CPSHouseholdInformation.new
      @household.householdDetails(@selenium, householdDetails,doc)
    end
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Household_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Household_next']"

    add_mother_information(doc)
    add_father_information(doc)
  end


  # Add parent/guardian information
  def add_mother_information doc
    guardianInfo = Array.new()
    guardianInfo = getMotherInfo(doc)

    if ! guardianInfo.empty? then
      #add details 
      @parent = CPSParentInformation.new
      @parent.motherDetails(@selenium, guardianInfo, doc)
    end
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Guardian-Mother_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Guardian-Mother_next']"
  end

  # Add parent/guardian information
  def add_father_information doc
    guardianInfo = Array.new()
    guardianInfo = getFatherInfo(doc)
    
    if ! guardianInfo.empty? then
      #add details 
      @parent = CPSParentInformation.new
      @parent.fatherDetails(@selenium, guardianInfo, doc)
    end
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Guardian-Father_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Guardian-Father_next']"
  end

  
  # Add sibling information
  def add_sibling_information doc
    siblingDetails = Array.new()
    @sibling = CPSSiblingInformation.new
    
    @doc.get_elements("StudentDetail/Student/number_sibling").collect{|sibling1|
      siblingDetails =  siblingDetails.push(sibling1)
    }
    
    #add details 
    if siblingDetails[0].txt =="1" then
      @selenium.open "drupal/civicrm/quest/cps/household?_qf_Sibling-1_display=true"
      siblingDetails = getSiblingInfo(doc)
      @sibling.sibling_1_details(@selenium, siblingDetails)
    elsif siblingDetails[0].txt =="2" then
      @selenium.open "drupal/civicrm/quest/cps/household?_qf_Sibling-1_display=true"
      siblingDetails = getSiblingInfo(doc)
      @sibling.sibling_1_details(@selenium, siblingDetails)
      @page.click_and_wait "document.forms['Sibling-1'].elements['_qf_Sibling-1_next'][1]"

      @selenium.open "drupal/civicrm/quest/cps/household?_qf_Sibling-2_display=true"
      siblingDetails = getSiblingInfo(doc)
      @sibling.sibling_2_details(@selenium, siblingDetails)
      @page.click_and_wait "document.forms['Sibling-2'].elements['_qf_Sibling-2_next'][1]"
    end

    
    #Submit the form 
    @page.click_and_wait "//input[@type='submit' and @name='document.forms['Sibling-1'].elements['_qf_Sibling-1_next'][1]']"
  end

  # Add income information
  def add_income_information doc
    assert_equal "Household Income", @selenium.get_text("link=Household Income")
    @page.click_and_wait "link=Household Income"
   # income_1_details(doc)
   # income_2_details(doc)
  end
  def income_1_details doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/household?_qf_Income-1_display=true"
    
    incomeDetails = Array.new() 
    incomeDetails = getIncome1Info(doc)
   
    #add details 
    @income = CPSIncomeInformation.new
    @income.income_1_Details(@selenium, incomeDetails, doc)
    
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Income-1_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Income-1_next']"

  end
  
  def income_2_details doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/household?_qf_Income-2_display=true"
    
    incomeDetails = Array.new() 
    incomeDetails = getIncome2Info(doc)
    
    #add details 
    @income = CPSIncomeInformation.new
    @income.income_1_Details(@selenium, incomeDetails, doc)
    
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Income-2_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Income-2_next']"
  end

  # Add High school information
  def add_highSchool_information doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/school?_qf_HighSchool_display=true"

    highSchoolInfo = Array.new() 
    highSchoolInfo = getHighSchoolInfo(doc)

    if ! highSchoolInfo.empty? then
      #add details 
      @highSchool = CPSHighschoolInformation.new
      @highSchool.highSchoolDetails(@selenium, highSchoolInfo, doc)
    end    
    #Submit the form 

    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_HighSchool_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_HighSchool_next']"
  end
  # Add other school information
  def add_otherSchool_information doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/school?_qf_SchoolOther_display=true"

    otherSchoolInfo = Array.new() 
    otherSchoolInfo = getOtherSchoolInfo(doc)
      
    if ! otherSchoolInfo.empty? then
      #add details 
      @otherschool = CPSOtherSchoolInformation.new
      @otherschool.otherSchoolDetails(@selenium, otherSchoolInfo, doc)
    end  
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_SchoolOther_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_SchoolOther_next']"
  end

  # Add academic information
  def add_academic_information doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/school?_qf_Academic_display=true"

    academicInfo = Array.new() 
    academicInfo = getAcademicInfo(doc)

     if ! academicInfo.empty? then
       #add details 
       @academic = CPSAcademicInformation.new
       @academic.academicDetails(@selenium, academicInfo, doc)
    end
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Academic_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Academic_upload']"
  end

  # Add Grade9 information
  def add_grade9_transcript doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/school?_qf_Transcript-Nine_display=true"
    
    grade9Details = Array.new()
    grade9Details = getGrade9Info(doc)
    
    if ! grade9Details.empty? then
      #add details 
      @trans9 = CPSGrade9Information.new
      @trans9.grade9Details(@selenium, grade9Details, doc)
    end
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Transcript-Nine_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Transcript-Nine_next']"
  end

  # Add Grade10 information
  def add_grade10_transcript doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/school?_qf_Transcript-Ten_display=true"

    grade10Details = Array.new()
    grade10Details = getGrade10Info(doc)

    if ! grade10Details.empty? then
      #add details 
      @grade10 = CPSGrade10Information.new
      @grade10.grade10Details(@selenium, grade10Details, doc)
    end 
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Transcript-Ten_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Transcript-Ten_next']"
  end

  # Add Grade11 information
  def add_grade11_transcript doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/school?_qf_Transcript-Eleven_display=true"

    grade11Details = Array.new()
    grade11Details = getGrade11Info(doc)
    
    if ! grade11Details.empty? then
      #add details 
      @grade11 = CPSGrade11Information.new
      @grade11.grade11Details(@selenium, grade11Details, doc)
    end
    
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Transcript-Eleven_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Transcript-Eleven_next']"
  end

  # Add Grade12 information
  def add_grade12_transcript doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/school?_qf_Transcript-Twelve_display=true"

    grade12Details = Array.new()
    grade12Details = getGrade12Info(doc)

    if ! grade12Details.empty? then
      #add details 
      @grade12 = CPSGrade12Information.new
      @grade12.grade12Details(@selenium, grade12Details, doc)
    end
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Transcript-Twelve_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Transcript-Twelve_next']"
  end

  # Add Summer School information
  def add_summer_school_transcript doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/school?_qf_Transcript-Summer_display=true"

    summerDetails = Array.new()
    summerDetails = getSummerInfo(doc)
    
    if ! summerDetails.empty? then
      #add details 
      @summerSchool = CPSSummerschoolInformation.new
      @summerSchool.summerSchoolDetails(@selenium, summerDetails, doc)
    end
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Transcript-Summer_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Transcript-Summer_next']"
  end

  # Add Testing information
  def add_testing_information doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/school?_qf_Testing_display=true"

    #add details 
    @testing = CPSTestingInformation.new
    @testing.testingDetails(@selenium,@doc)    
    
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Testing_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Testing_next']"
  end

  # Add Biographical Essay information
  def add_biographical_essay doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/essay?_qf_Essay-Biographical_display=true"

    essayBiographical = Array.new()
    @doc.get_elements("StudentDetail/Essay/cm_essay_biographical/biographical").collect{|essay| 
      essayBiographical = essayBiographical.push(essay)
    }
    if ! essayBiographical.empty? then
      #add essay details 
      @essay = CPSEssay.new
      @essay.biographicalEssay(@selenium, essayBiographical)
    end
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Essay-Biographical_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Essay-Biographical_next']"
  end

  # Add Optional Essay information
  def add_optional_essay doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/essay?_qf_Essay-Optional_display=true"

    essayOptional = Array.new()

    @doc.get_elements("StudentDetail/Essay/cm_essay_optional/optional").collect{|essay|
      essayOptional = essayOptional.push(essay)
    }
    if ! essayOptional.empty? then
      #add essay details 
      @essayOptional = CPSEssay.new
      @essayOptional.optionalEssay(@selenium, essayOptional)
    end
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Essay-Optional_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Essay-Optional_next']"
  end

  # Add College Match Ranking information
  def add_college_match_ranking doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/college?_qf_CmRanking_display=true"

    partner = Array.new()
    partner = getPartnerRanking(doc)

    if ! partner.empty? then
      #add details 
      @ranking = CPSMatchRankingInformation.new
      @ranking.matchRankingDetails(@selenium, partner)
    end
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_CmRanking_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_CmRanking_next']"
  end

  # Add Application Forwarding information
  def add_application_forwarding doc
    #open URL
    @selenium.open "/drupal/civicrm/quest/cps/college?_qf_ForwardApp_display=true"

    appForward = Array.new()
    appForward = getApplicationFwd(doc)

    if ! appForward.empty? then
      #add details 
      @applicationFwd = CPSAppForwardingInformation.new
      @applicationFwd.appForwardingDetails(@selenium, appForward)
    end
    #Submit the form 
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_ForwardApp_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_ForwardApp_next']"
  end

  # Save Draft information
  def save_draft doc
    assert_equal "Save Draft", @selenium.get_text("link=Save Draft")
    @page.click_and_wait "link=Save Draft"

    #add details 
    @selenium.check "is_partner_share"
    if @selenium.check "is_recommendation_waived" then
          @selenium.check "is_recommendation_waived"
    else
      @selenium.check "document.Submit.is_recommendation_waived[1]"
    end

    #Submit Application
    assert_equal "Save & Continue", @selenium.get_value("//input[@type='submit' and @name='_qf_Submit_next']")
    @page.click_and_wait "//input[@type='submit' and @name='_qf_Submit_next]"
  end
  
  #Submit Application
  def submit_application doc
    assert_equal "Submit Application", @selenium.get_text("link=Submit Application")
    @page.click_and_wait "link=Submit Application"

  end

  def getStudentInfo(doc)
    studentInfo = Array.new()
    #collect details in an array
    @doc.get_elements("StudentDetail/Individual").collect{|individual|
      individual.each_element{|elem| 
        studentInfo = studentInfo.push(elem)
      }
    }
    @doc.get_elements("StudentDetail/Address_1").collect{|a1|
      a1.each_element{|addr1| 
        studentInfo = studentInfo.push(addr1)
      }
    }
    @doc.get_elements("StudentDetail/Phone_1_Main").collect{|ph1|
      studentInfo = studentInfo.push(ph1)
    }
    @doc.get_elements("StudentDetail/Phone_1_Alt").collect{|phAlt1|
      studentInfo = studentInfo.push(phAlt1)
      }
    @doc.get_elements("StudentDetail/Phone_2_Main").collect{|ph2|
        studentInfo = studentInfo.push(ph2)
    }
    @doc.get_elements("StudentDetail/Address_2").collect{|a2|
      a2.each_element{|addr2| 
        studentInfo = studentInfo.push(addr2)
      }
    }
    @doc.get_elements("StudentDetail/Student/years_in_us").collect{|st|
        studentInfo = studentInfo.push(st)
    }
 
    @doc.get_elements("StudentDetail/Student/first_language").collect{|st|
      studentInfo = studentInfo.push(st)
    }
    @doc.get_elements("StudentDetail/Student/high_school_grad_year").collect{|st|
      studentInfo = studentInfo.push(st)
    }
    @doc.get_elements("StudentDetail/Student/number_siblings").collect{|st|
      studentInfo = studentInfo.push(st)
    }
    @doc.get_elements("StudentDetail/Student/citizenship_country").collect{|st|
      studentInfo = studentInfo.push(st)
    }
    @doc.get_elements("StudentDetail/Student/growup_country").collect{|st|
      studentInfo = studentInfo.push(st)
    }
    @doc.get_elements("StudentDetail/Student/first_language").collect{|st|
      studentInfo = studentInfo.push(st)
    }
    @doc.get_elements("StudentDetail/Student/primary_language").collect{|st|
      studentInfo = studentInfo.push(st)
    }
    @doc.get_elements("StudentDetail/Student/ethnicity_1").collect{|st|
      studentInfo = studentInfo.push(st)
    }
    @doc.get_elements("StudentDetail/Student/ethnicity_2").collect{|st|
      studentInfo = studentInfo.push(st)
    }
    @doc.get_elements("StudentDetail/Student/citizenship_status").collect{|st|
      studentInfo = studentInfo.push(st)
    }
    
    return studentInfo
  end
  
  def getWorkExperience doc
    workExpDetails = Array.new()
    @doc.get_elements("StudentDetail/WorkExperience_1").collect{|work|
      work.each_element{|workExp| 
        workExpDetails = workExpDetails.push(workExp)
      }
    }
    @doc.get_elements("StudentDetail/WorkExperience_2").collect{|work|
      work.each_element{|workExp| 
        workExpDetails = workExpDetails.push(workExp)
      }
    }
    @doc.get_elements("StudentDetail/WorkExperience_3").collect{|work|
      work.each_element{|workExp| 
        workExpDetails = workExpDetails.push(workExp)
      }
    }
    @doc.get_elements("StudentDetail/WorkExperience_4").collect{|work|
      work.each_element{|workExp| 
        workExpDetails = workExpDetails.push(workExp)
      }
    }
    @doc.get_elements("StudentDetail/WorkExperience_5").collect{|work|
      work.each_element{|workExp| 
        workExpDetails = workExpDetails.push(workExp)
      }
    }
    return workExpDetails
  end

  def getExtracurricularInfo(doc)
    curricularInfo = Array.new()
    
    @doc.get_elements("StudentDetail/Essay/cm_extracurricular_info/meaningful_commitment").collect{|essay|
      curricularInfo = curricularInfo.push(essay)
    }
    @doc.get_elements("StudentDetail/Essay/cm_extracurricular_info/past_activities").collect{|essay|
      curricularInfo = curricularInfo.push(essay)
    }
    @doc.get_elements("StudentDetail/Essay/cm_extracurricular_info/hobbies").collect{|essay|
      curricularInfo = curricularInfo.push(essay)
    }
        
    @doc.get_elements("StudentDetail/Extracurricular_1").collect{|curricular|
      curricular.each_element{|info| 
        curricularInfo = curricularInfo.push(info)
      }
    }
    @doc.get_elements("StudentDetail/Extracurricular_2").collect{|curricular|
      curricular.each_element{|info| 
        curricularInfo = curricularInfo.push(info)
      }
    }
    @doc.get_elements("StudentDetail/Extracurricular_3").collect{|curricular|
      curricular.each_element{|info| 
        curricularInfo = curricularInfo.push(info)
      }
    }
    @doc.get_elements("StudentDetail/Extracurricular_4").collect{|curricular|
      curricular.each_element{|info| 
        curricularInfo = curricularInfo.push(info)
      }
    }
    @doc.get_elements("StudentDetail/Extracurricular_5").collect{|curricular|
      curricular.each_element{|info| 
        curricularInfo = curricularInfo.push(info)
      }
    }
    @doc.get_elements("StudentDetail/Extracurricular_6").collect{|curricular|
      curricular.each_element{|info| 
        curricularInfo = curricularInfo.push(info)
      }
    }
    @doc.get_elements("StudentDetail/Extracurricular_7").collect{|curricular|
      curricular.each_element{|info| 
        curricularInfo = curricularInfo.push(info)
      }
    }
    @doc.get_elements("StudentDetail/Student/varsity_sports_list").collect{|curricular|
      curricularInfo = curricularInfo.push(curricular)
    }
    @doc.get_elements("StudentDetail/Student/arts_list").collect{|curricular|
      curricularInfo = curricularInfo.push(curricular)
    }
    
    return curricularInfo
  end
  def getGuardianInfo(doc)
    guardianInfo =Array.new()

    @doc.get_elements("StudentDetail/Guardian_1").collect{|guardian|
      guardian.each_element{|g1| 
        guardianInfo = guardianInfo.push(g1)
      }
    }
    
    @doc.get_elements("StudentDetail/Guardian_2").collect{|guardian|
      guardian.each_element{|g1| 
        guardianInfo = guardianInfo.push(g1)
      }
    }
    return guardianInfo
  end
  def getHighSchoolInfo doc
    schoolInfo = Array.new()

    @doc.get_elements("StudentDetail/HighSchool_1").collect{|school|
      school.each_element{|s1| 
        schoolInfo = schoolInfo.push(s1)
      }
    }
    @doc.get_elements("StudentDetail/HighSchool_2").collect{|school|
      school.each_element{|s1| 
        schoolInfo = schoolInfo.push(s1)
      }
    }
    @doc.get_elements("StudentDetail/HighSchool_3").collect{|school|
      school.each_element{|s1| 
        schoolInfo = schoolInfo.push(s1)
      }
    }
    return schoolInfo
  end
  
  def getOtherSchoolInfo doc
    otherSchoolInfo = Array.new()
    
    @doc.get_elements("StudentDetail/OtherSchool_1").collect{|school|
      school.each_element{|s1| 
        otherSchoolInfo = otherSchoolInfo.push(s1)
      }
    }
    @doc.get_elements("StudentDetail/OtherSchool_2").collect{|school|
      school.each_element{|s1| 
        otherSchoolInfo = otherSchoolInfo.push(s1)
      }
    }
    @doc.get_elements("StudentDetail/OtherSchool_3").collect{|school|
      school.each_element{|s1| 
        otherSchoolInfo = otherSchoolInfo.push(s1)
      }
    }
    @doc.get_elements("StudentDetail/OtherSchool_4").collect{|school|
      school.each_element{|s1| 
        otherSchoolInfo = otherSchoolInfo.push(s1)
      }
    }
    @doc.get_elements("StudentDetail/OtherSchool_5").collect{|school|
      school.each_element{|s1| 
        otherSchoolInfo = otherSchoolInfo.push(s1)
      }
    }
    return otherSchoolInfo
  end

  def getAcademicInfo doc
    academicInfo = Array.new()
    
    @doc.get_elements("StudentDetail/Student/gpa_unweighted").collect{|academic|
      academicInfo = academicInfo.push(academic)
      }
    
    @doc.get_elements("StudentDetail/Student/gpa_weighted").collect{|academic|
      academicInfo = academicInfo.push(academic)
    }

    @doc.get_elements("StudentDetail/Student/is_class_ranking").collect{|academic|
      academicInfo = academicInfo.push(academic)
    }

    @doc.get_elements("StudentDetail/Student/class_rank_percent").collect{|academic|
      academicInfo = academicInfo.push(academic)
    }
 
    @doc.get_elements("StudentDetail/Student/class_rank").collect{|academic|
      academicInfo = academicInfo.push(academic)
    }

    @doc.get_elements("StudentDetail/Student/class_num_students").collect{|academic|
      academicInfo = academicInfo.push(academic)
    }

    @doc.get_elements("StudentDetail/Student/gpa_explanation").collect{|academic|
      academicInfo = academicInfo.push(academic)
    }

    @doc.get_elements("StudentDetail/Honor_1").collect{|academic|
      academic.each_element{|a1| 
        academicInfo = academicInfo.push(a1)
      }
    }
    @doc.get_elements("StudentDetail/Honor_2").collect{|academic|
      academic.each_element{|a1| 
        academicInfo = academicInfo.push(a1)
      }
    }
    @doc.get_elements("StudentDetail/Honor_3").collect{|academic|
      academic.each_element{|a1| 
        academicInfo = academicInfo.push(a1)
      }
    }

    @doc.get_elements("StudentDetail/Honor_4").collect{|academic|
      academic.each_element{|a1| 
        academicInfo = academicInfo.push(a1)
      }
    }
    @doc.get_elements("StudentDetail/Honor_5").collect{|academic|
      academic.each_element{|a1| 
        academicInfo = academicInfo.push(a1)
      }
    }
    @doc.get_elements("StudentDetail/Honor_6").collect{|academic|
      academic.each_element{|a1| 
        academicInfo = academicInfo.push(a1)
      }
    }
    return academicInfo
  end
  
  def getGrade9Info doc 
    grade9Details = Array.new

    @doc.get_elements("StudentDetail/transcript_Nine/course_title_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_credit_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_honor_status_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_1_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_1_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_1_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_1_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_subject_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Nine/course_title_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_credit_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_honor_status_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_2_1").collect{|grade10|
      grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_2_2").collect{|grade10|
      grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_2_3").collect{|grade10|
      grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_2_4").collect{|grade10|
      grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_subject_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Nine/course_title_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_credit_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_honor_status_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_3_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_3_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_3_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_3_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_subject_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/course_title_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_credit_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_honor_status_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_4_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_4_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_4_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_4_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_subject_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/course_title_5").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_credit_5").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_honor_status_5").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_5_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_5_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_5_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_5_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_subject_5").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Nine/course_title_6").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_credit_6").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_honor_status_6").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_6_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_6_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_6_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_6_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_subject_6").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Nine/course_title_7").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_credit_7").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_honor_status_7").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_7_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_7_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_7_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_7_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_subject_7").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Nine/course_title_8").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_credit_8").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_honor_status_8").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_8_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_8_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_8_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_8_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_subject_8").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Nine/course_title_9").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_credit_9").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_honor_status_9").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_9_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_9_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_9_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_9_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_subject_9").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    
    @doc.get_elements("StudentDetail/transcript_Nine/course_title_10").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_credit_10").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_honor_status_10").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_10_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_10_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_10_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_10_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_subject_10").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Nine/course_title_11").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_credit_11").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_honor_status_11").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_11_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_11_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_11_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_11_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_subject_11").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Nine/course_title_12").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_credit_12").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_honor_status_12").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_12_1").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_12_2").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_12_3").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/grade_12_4").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Nine/academic_subject_12").collect{|grade10|
        grade9Details = grade9Details.push(grade10)
    }
    return grade9Details
  end
  
  def getGrade10Info doc 
    grade10Details = Array.new

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_1").collect{|grade10|
      if grade10.has_text? then
        grade10Details = grade10Details.push(grade10)
      else
        grade10Details = grade10Details.push("")
      end
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_1").collect{|grade10|
      if grade10.has_text? then
        grade10Details = grade10Details.push(grade10)
      else
        grade10Details = grade10Details.push("")
      end
    } 
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_1").collect{|grade10|
      if grade10.has_text? then
        grade10Details = grade10Details.push(grade10)
      else
        grade10Details = grade10Details.push("")
      end
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_1_1").collect{|grade10|
      if grade10.has_text? then
        grade10Details = grade10Details.push(grade10)
      else
        grade10Details = grade10Details.push("")
      end  
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_1_2").collect{|grade10|
      if grade10.has_text? then
        grade10Details = grade10Details.push(grade10)
      else
        grade10Details = grade10Details.push("")
      end  
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_1_3").collect{|grade10|
      if grade10.has_text? then
        grade10Details = grade10Details.push(grade10)
      else
        grade10Details = grade10Details.push("")
      end  
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_1_4").collect{|grade10|
      if grade10.has_text? then
        grade10Details = grade10Details.push(grade10)
      else
        grade10Details = grade10Details.push("")
      end  
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_1").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_2").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_2").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_2").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_2_1").collect{|grade10|
      grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_2_2").collect{|grade10|
      grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_2_3").collect{|grade10|
      grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_2_4").collect{|grade10|
      grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_2").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_3").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_3").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_3").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_3_1").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_3_2").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_3_3").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_3_4").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_3").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/course_title_4").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_4").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_4").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_4_1").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_4_2").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_4_3").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_4_4").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_4").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/course_title_5").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_5").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_5").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_5_1").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_5_2").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_5_3").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_5_4").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_5").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_6").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_6").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_6").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_6_1").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_6_2").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_6_3").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_6_4").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_6").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_7").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_7").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_7").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_7_1").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_7_2").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_7_3").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_7_4").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_7").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_8").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_8").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_8").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_8_1").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_8_2").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_8_3").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_8_4").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_8").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_9").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_9").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_9").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_9_1").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_9_2").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_9_3").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_9_4").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_9").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    
    @doc.get_elements("StudentDetail/transcript_Ten/course_title_10").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_10").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_10").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_10_1").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_10_2").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_10_3").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_10_4").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_10").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_11").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_11").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_11").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_11_1").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_11_2").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_11_3").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_11_4").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_11").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_12").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_12").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_12").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_12_1").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_12_2").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_12_3").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_12_4").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_12").collect{|grade10|
        grade10Details = grade10Details.push(grade10)
    }
    return grade10Details
  end

  def getGrade11Info doc 
    grade11Details = Array.new

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_1_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_1_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_1_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_1_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_2_1").collect{|grade11|
      grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_2_2").collect{|grade11|
      grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_2_3").collect{|grade11|
      grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_2_4").collect{|grade11|
      grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_3_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_3_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_3_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_3_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/course_title_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_4_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_4_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_4_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_4_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/course_title_5").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_5").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_5").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_5_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_5_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_5_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_5_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_5").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_6").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_6").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_6").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_6_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_6_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_6_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_6_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_6").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_7").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_7").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_7").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_7_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_7_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_7_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_7_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_7").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_8").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_8").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_8").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_8_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_8_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_8_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_8_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_8").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_9").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_9").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_9").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_9_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_9_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_9_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_9_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_9").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    
    @doc.get_elements("StudentDetail/transcript_Ten/course_title_10").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_10").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_10").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_10_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_10_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_10_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_10_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_10").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_11").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_11").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_11").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_11_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_11_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_11_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_11_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_11").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }

    @doc.get_elements("StudentDetail/transcript_Ten/course_title_12").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_credit_12").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_honor_status_12").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_12_1").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_12_2").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_12_3").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/grade_12_4").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    @doc.get_elements("StudentDetail/transcript_Ten/academic_subject_12").collect{|grade11|
        grade11Details = grade11Details.push(grade11)
    }
    return grade11Details
  end

 def getGrade12Info doc 
    grade12Details = Array.new

    @doc.get_elements("StudentDetail/transcript_Twelve/course_title_1").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_credit_1").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_honor_status_1").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_subject_1").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }

    @doc.get_elements("StudentDetail/transcript_Twelve/course_title_2").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_credit_2").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_honor_status_2").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_subject_2").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }

    @doc.get_elements("StudentDetail/transcript_Twelve/course_title_3").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_credit_3").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_honor_status_3").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_subject_3").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/course_title_4").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_credit_4").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_honor_status_4").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_subject_4").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/course_title_5").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_credit_5").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_honor_status_5").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_subject_5").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }

    @doc.get_elements("StudentDetail/transcript_Twelve/course_title_6").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_credit_6").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_honor_status_6").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_subject_6").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }

    @doc.get_elements("StudentDetail/transcript_Twelve/course_title_7").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_credit_7").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_honor_status_7").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_subject_7").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }

    @doc.get_elements("StudentDetail/transcript_Twelve/course_title_8").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_credit_8").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_honor_status_8").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_subject_8").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }

    @doc.get_elements("StudentDetail/transcript_Twelve/course_title_9").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_credit_9").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_honor_status_9").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_subject_9").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    
    @doc.get_elements("StudentDetail/transcript_Twelve/course_title_10").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_credit_10").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_honor_status_10").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_subject_10").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }

    @doc.get_elements("StudentDetail/transcript_Twelve/course_title_11").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_credit_11").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_honor_status_11").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_subject_11").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }

    @doc.get_elements("StudentDetail/transcript_Twelve/course_title_12").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_credit_12").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_honor_status_12").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    @doc.get_elements("StudentDetail/transcript_Twelve/academic_subject_12").collect{|grade12|
        grade12Details = grade12Details.push(grade12)
    }
    return grade12Details
  end

 def getPartnerRanking doc
   partner = Array.new()
   @doc.get_elements("StudentDetail/PartnerRanking/Amherst_College_Ranking").collect{|matchRanking|
     partner = partner.push(matchRanking)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Bowdoin_College_Ranking").collect{|matchRanking|
     partner = partner.push(matchRanking)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Claremont_McKenna_College_Ranking").collect{|matchRanking|
     partner = partner.push(matchRanking)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Columbia_University_Ranking").collect{|matchRanking|
       partner = partner.push(matchRanking)
     }
   @doc.get_elements("StudentDetail/PartnerRanking/Oberlin_College_Ranking").collect{|matchRanking|
       partner = partner.push(matchRanking)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Pomona_College_Ranking").collect{|matchRanking|
       partner = partner.push(matchRanking)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Princeton_University_Ranking").collect{|matchRanking|
       partner = partner.push(matchRanking)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Rice_University_Ranking").collect{|matchRanking|
       partner = partner.push(matchRanking)
    }
   @doc.get_elements("StudentDetail/PartnerRanking/Scripps_College_Ranking").collect{|matchRanking|
       partner = partner.push(matchRanking)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Stanford_University_Ranking").collect{|matchRanking|
       partner = partner.push(matchRanking)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Swarthmore_College_Ranking").collect{|matchRanking|
       partner = partner.push(matchRanking)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Trinity_College_Ranking").collect{|matchRanking|
       partner = partner.push(matchRanking)
     }
   @doc.get_elements("StudentDetail/PartnerRanking/Wellesley_College_Ranking").collect{|matchRanking|
       partner = partner.push(matchRanking)
     }
   @doc.get_elements("StudentDetail/PartnerRanking/Wheaton_College_Ranking").collect{|matchRanking|
       partner = partner.push(matchRanking)
     }
   @doc.get_elements("StudentDetail/PartnerRanking/Williams_College_Ranking").collect{|matchRanking|
     partner = partner.push(matchRanking)
     }
   return partner
 end
 
 def getApplicationFwd doc
   appForward = Array.new()
   
   @doc.get_elements("StudentDetail/PartnerRanking/Amherst_College_Forward").collect{|forward|
     appForward = appForward.push(forward)
  }
   @doc.get_elements("StudentDetail/PartnerRanking/Amherst_College_Forward").collect{|forward|
    appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Bowdoin_College_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Claremont_McKenna_College_Forward").collect{|forward|
    appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Columbia_University_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Oberlin_College_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Pomona_College_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Princeton_University_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Rice_University_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Scripps_College_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Stanford_University_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Swarthmore_College_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Trinity_College_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Wellesley_College_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Wheaton_College_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Williams_College_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/Gates_Millennium_Scholarship_Forward").collect{|forward|
     appForward = appForward.push(forward)
  }
   @doc.get_elements("StudentDetail/PartnerRanking/The_Hispanic_Scholarship_Fund_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/The_Hispanic_College_Fund_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   @doc.get_elements("StudentDetail/PartnerRanking/The_Donna_Reed_Foundation_Scholarship_Forward").collect{|forward|
     appForward = appForward.push(forward)
   }
   return appForward
 end

 def getHouseholdDetails doc
   householdDetails = Array.new()
    @doc.get_elements("StudentDetail/Household_Current").collect{|household|
      household.each_element{|hs| 
        householdDetails = householdDetails.push(hs)
      }
    }
   
   @doc.get_elements("StudentDetail/Household_Previous").collect{|household1|
      household1.each_element{|hsPrevious| 
        householdDetails = householdDetails.push(hsPrevious)
      }
    }
   @doc.get_elements("StudentDetail/Guardian_1/relationship").collect{|household1|
        householdDetails = householdDetails.push(household1)
   }
   @doc.get_elements("StudentDetail/Guardian_2/relationship").collect{|household1|
        householdDetails = householdDetails.push(household1)
   }
   return householdDetails 
 end

 def getFatherInfo doc
   guardianInfo = Array.new()
   @doc.get_elements("StudentDetail/Guardian_1").collect{|guardian|
      guardian.each_element{|guardian1| 
        guardianInfo = guardianInfo.push(guardian1)
      }
    }
   return guardianInfo
 end

 def getMotherInfo doc
   guardianInfo = Array.new()
   @doc.get_elements("StudentDetail/Guardian_2").collect{|guardian|
      guardian.each_element{|guardian1| 
        guardianInfo = guardianInfo.push(guardian1)
      }
    }
   return guardianInfo
 end
 
def getIncome1Info doc
   incomeInfo = Array.new()
   @doc.get_elements("StudentDetail/Income_1").collect{|income1|
      income1.each_element{|income2| 
        incomeInfo = incomeInfo.push(income2)
      }
    }
   return incomeInfo
 end

def getIncome2Info doc
   incomeInfo = Array.new()
   @doc.get_elements("StudentDetail/Income_2").collect{|income1|
      income1.each_element{|income2| 
        incomeInfo = incomeInfo.push(income2)
      }
    }
   return incomeInfo
 end

def getSibling1Info doc
  siblingDetails = Array.new()
  @doc.get_elements("StudentDetail/Student/Sibling1").collect{|sibling1|
    sibling1.each_element{|sibling2| 
      siblingDetails =  siblingDetails.push(sibling2)
    }
  }
  return  siblingDetails
 end

def getSibling2Info doc
   siblingDetails = Array.new()
   @doc.get_elements("StudentDetail/Student/Sibling_2").collect{|sibling1|
      sibling1.each_element{|sibling2| 
         siblingDetails =  siblingDetails.push(sibling2)
      }
    }
   return  siblingDetails
 end
 
end
