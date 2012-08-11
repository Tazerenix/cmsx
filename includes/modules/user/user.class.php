<?php

class user
{
    use dbOwner;
    use sessionOwner;
    
    const ANONYMOUS = 0;
    
    private $userType;
    private $userDetails;
    
    public function __construct()
    {
        
    }
    
    public function setUser($user_id)
    {
        if (!$user_id)
        {
            if ($this->session->getVal('user_id')) // is user logged in?
            {
                $this->userType = $this->db->getResult('SELECT user_type FROM '.TBL_USER.' WHERE user_id = :id', array(':id' => $this->session->getval('user_id')));
                $this->setUserDetails($user_id);
                $this->updateLastvisit();
            }
            else
            {
                $this->userType = self::ANONYMOUS;
            }
        }
        
        return $this;
    }
    
    public function isLoggedin()
    {
        return (bool)$this->session->getVal('user_id');
    }
    
    private function setUserDetails($user_id)
    {
        $this->userDetails = (object)$this->db->getRow('SELECT user_id, 
                                                               user_name, 
                                                               user_slug, 
                                                               user_email,
                                                               user_created,
                                                               user_lastvisit,
                                                               user_timezone,
                                                               user_birthday,
                                                               user_location
                                                               FROM '.TBL_USER.' WHERE user_id = :id', array(':id' => $user_id));
        return $this;
    }
    
    private function updateLastvisit()
    {
        
    }
}

?>