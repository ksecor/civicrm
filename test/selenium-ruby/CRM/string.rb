# for Array#get_random
require 'array'

class String

  # add random string generator (a class method)
  def self.gen_random size = 7, letters = ('a'..'z').to_a
    string = ''
    size.times { string += letters.get_random }
    string
  end

end
