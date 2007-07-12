# This is a test case of using Selenium and its Ruby bindings

#class to add contribution details
class CRMContributionSearchDetails

  #add details for contribution search
  def searchContribution selenium
    selenium.select "contribution_date_low[M]", "label=May"
    selenium.select "contribution_date_low[d]", "label=16"
    selenium.select "contribution_date_low[Y]", "label=2005"
    selenium.select "contribution_date_high[M]", "label=Jun"
    selenium.select "contribution_date_high[d]", "label=27"
    selenium.select "contribution_date_high[Y]", "label=2006"
    selenium.type "contribution_amount_low", "50"
    selenium.type "contribution_amount_high", "150"
    selenium.select "contribution_type_id", "label=Donation"
    selenium.select "contribution_payment_instrument_id", "label=CreditCard"
  end
end

#class to add contribution details
class CRMMembershipSearchDetails

  #add details for contribution search
  def searchMember selenium
    selenium.check "member_membership_type_id[1]"
    selenium.check "member_status_id[2]"
    selenium.check "member_status_id[1]"
    selenium.type "member_source", "Donation"
    selenium.select "member_start_date_low[M]", "label=Jan"
    selenium.select "member_start_date_low[d]", "label=01"
    selenium.select "member_start_date_low[Y]", "label=2005"
    selenium.select "member_start_date_high[M]", "label=Dec"
    selenium.select "member_start_date_high[d]", "label=31"
    selenium.select "member_start_date_high[Y]", "label=2006"
    selenium.select "member_end_date_low[M]", "label=Dec"
    selenium.select "member_start_date_high[M]", "label=Jan"
    selenium.select "member_start_date_high[d]", "label=30"
    selenium.select "member_end_date_low[d]", "label=31"
    selenium.select "member_end_date_low[Y]", "label=2006"
    selenium.select "member_end_date_high[M]", "label=Dec"
    selenium.select "member_end_date_high[d]", "label=31"
    selenium.select "member_end_date_high[Y]", "label=2006"
  end
end

