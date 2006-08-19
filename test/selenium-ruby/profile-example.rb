#!/usr/bin/env ruby

# This is an (obviously - ugly) example of using Selenium and its Ruby bindings
# to register a randomly-filled profile, using the default CiviCRM 'Constituent
# Information' profile definition

require 'crm_page_controller'

require 'crm_config'
config = CRMConfig.new

# if you want to use random words instead of random strings, help yourself
# here (and replace String.gen_random with words.get_random below)
# note: some sanitizing might be required - e.g., emails can't have apostrophes
# words = []
# File.open('/usr/share/dict/words').each do |word|
#   words << word.chomp
# end

# for Array#get_random_and_delete
require 'array'

# for String.gen_random
require 'string'

# how many concurrent browser instances should be run?
instances = 1

threads = []

instances.times do
  threads << Thread.new do

    # go to the 'Create new account' Drupal screen
    profile = CRMPageController.new 'localhost', 4444, config.browser, config.uf_root
    profile.start
    profile.open config.login_url
    profile.click_and_wait 'link=Create new account'

    # fill the forms with random data
    # - Account information
    profile.type 'edit[name]', String.gen_random
    profile.type 'edit[mail]', String.gen_random + '@' + config.email_domain

    # - Constituent Information
    profile.type 'first_name',       String.gen_random
    profile.type 'last_name',        String.gen_random
    profile.type 'street_address-1', String.gen_random
    profile.type 'city-1',           String.gen_random
    profile.type 'postal_code-1',    rand(90000) + 10000

    # - - with random state out of the first ten (we don't know the length of the list)
    profile.select 'state_province-1', "index=#{rand(10) + 1}"
    profile.select 'country-1',        "index=#{rand(240) + 1}"

    # - - with random Most Important Issue
    profile.click "document.forms[0].custom_5[#{rand 3}]"

    # - - with random set of GOTV Experience
    exps = ['HM', 'PB', 'PW', 'SB']
    (rand(exps.size + 1)).times do
      profile.click "custom_6[#{exps.get_random_and_delete}]"
    end

    # - - with random Marital Status
    profile.select 'custom_7', "index=#{rand(5) + 1}"

    # uncomment to make the script stop here and wait for an enter on
    # the command line - this way you can see how the form got filled
#   gets

    # submit the form and exit
    profile.click_and_wait 'op'
    profile.stop
  end
end

# wait for all of the threads to finish
threads.each { |thread| thread.join }
