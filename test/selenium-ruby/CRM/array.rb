class Array

  # random array element retriever
  def get_random
    self[rand(size)]
  end

  # random array element popper
  def get_random_and_delete
    delete_at rand(size)
  end

end
