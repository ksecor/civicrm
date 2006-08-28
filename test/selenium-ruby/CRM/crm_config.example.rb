class CRMConfig

  def initialize
    @config = {
        # which browser should Selenium use
        # this is a nice default for Debian/Ubuntu
        'browser'      => '*firefox /opt/firefox-1.5/firefox-bin',

        # what's the root of the Drupal's framework
        'uf_root'      => 'http://192.168.2.14/drupal/',

        # where's the login URL
        'login_url'    => '/',

        # Drupal login details
        'user'         => 'abhilasha',
        'pass'         => 'abhi',

        # what should be the domain of login emails
        'email_domain' => 'localhost',
    }
  end

  # allow accessing config settings via CRMConfig#setting
  def method_missing method
    @config[method.to_s]
  end

end
