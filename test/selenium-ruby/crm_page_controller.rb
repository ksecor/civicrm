require 'selenium'

class CRMPageController < Selenium::SeleneseInterpreter

  def click_and_wait what_to_click
    click what_to_click
    wait_for_page_to_load 30000
  end

  # login to Drupal
  def login start_url, user, pass
    open start_url
    type 'edit[name]', user
    type 'edit[pass]', pass
    click_and_wait 'op'
  end

  # log out of Drupal
  def logout
    click_and_wait 'link=log out'
  end

end
