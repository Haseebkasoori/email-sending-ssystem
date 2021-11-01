<?php
class Validator
{
    //checks if data has any value except alphabets
    public $alpha_pattern = "/[^A-Za-z ]+/i";       
    
    //checks if data has any value except lower case alphabets
    public $lower_alpha_pattern = "/[^a-z ]+/i";       
    
    //checks if data has any value except upper case alphabets
    public $upper_alpha_pattern = "/[^A-Z ]+/i";       

    //checks if data has any value except alphabets and numaric
    public $alpha_numaric_pattern = "/([A-Za-z0-9_-])\w+/";       

    //checks if data has any value except integers
    public $numaric_pattern = "/[^0-9]+/";  
    
    //checks if data has any value except 
    public $special_pattern = "/[^\w]+/";  
    //check if Password at least 8 characters in length and should include at least one upper case letter, one number, and one special character.
    
    public function contain_non_alphabet($name)
    {
        return preg_match($this->alpha_pattern, $name);
    }
    public function contain_non_alpha_numaric($user_name)
    {
        return preg_match($this->alpha_numaric_pattern, $user_name);
    }

    public function contain_non_integer($contact_num)
    {
        return preg_match($this->numaric_pattern, $contact_num);
    }

    public function validate_password($password)
    {
        // Validate password strength
        $uppercase = preg_match($this->upper_alpha_pattern, $password);
        $lowercase = preg_match($this->lower_alpha_pattern, $password);
        $number    = preg_match($this->numaric_pattern , $password);
        $specialChars = preg_match($this->special_pattern, $password);

        if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            return false;
        }else{
            return true;
        }
    }

    public function validate_email($email)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
            return true;
        } else {
            return false;
        }
    }
}
