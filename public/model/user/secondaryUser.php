<?php
class SecondaryUser
{
    private $id;
    private $merchant_id;
    private $user_name;
    private $email;
    private $password;
    private $first_name;
    private $last_name;
    private $phone_number;
    private $address;
    private $user_ion_id;
    private $stripe_customer_id;
    private $forgot_otp;
    private $profile_image;
    private $email_sending;
    private $cradit_recharge;
    

    public function set_user($merchant_id,$user_name,$email,$password,$first_name,$last_name,$phone_number,$address,$user_ion_id,$profile_image,$email_sending,$cradit_recharge)
    {
        $this->merchant_id   = $merchant_id;
        $this->user_name    = $user_name;
        $this->email        = $email;
        $this->password     = $password;
        $this->first_name   = $first_name;
        $this->last_name    = $last_name;
        $this->phone_number = $phone_number;
        $this->address      = $address;
        $this->user_ion_id  = $user_ion_id;        
        $this->profile_image  = $profile_image;
        $this->email_sending  = $email_sending;
        $this->cradit_recharge  = $cradit_recharge;
    }

    public function get_user()
    {
        $user = array(
            "merchant_id" => $this->$merchant_id,
            "user_name" => $this->$user_name,
            "email" => $this->$email,
            "password" => $this->$password,
            "first_name" => $this->$first_name,
            "last_name" => $this->$last_name,
            "phone_number" => $this->$phone_number,
            "address" => $this->$address,
            "user_ion_id" => $this->$user_ion_id,
            "stripe_customer_id" => $this->$stripe_customer_id,
            "forgot_otp" => $this->$forgot_otp,
            "profile_image" => $this->$profile_image,
            "email_sending" => $this->$email_sending,
            "cradit_recharge" => $this->$cradit_recharge
        );
        return $user;
    }
    public function sign_up($db_conn)
    {
        $query="INSERT INTO secondary_user (merchant_id,user_name,email,password,first_name,last_name,phone_number,address,user_ion_id,profile_image,email_sending,cradit_recharge) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
        $query=$db_conn->prepare($query);
        $query->bind_param("ssssssisssii", $this->merchant_id,$this->user_name,$this->email,$this->password,$this->first_name,$this->last_name,$this->phone_number,$this->address,$this->user_ion_id,$this->profile_image,$this->email_sending,$this->cradit_recharge);
        $query->execute();
        if ($query->affected_rows >0){
            return true;
        }
        else{
            return false;
        }
        $db_conn->close();
    }
    public function add_credit($user_ion_id,$cretid,$day_limit, $db_conn){
        
        $query="INSERT INTO user_credit (merchant_ion_id) VALUES (?)";
        $query=$db_conn->prepare($query);
        $query->bind_param("s", $this->user_ion_id);
        $query->execute();
        
        if ($query->affected_rows >0){
            return true;
        }
        else{
            return false;
        }
        $db_conn->close();       

    }
    public function check_user($user_ion_id,$db_conn)
    {
      //create query
        $email_check = $db_conn->prepare("SELECT * FROM secondary_user WHERE user_ion_id=?");
        $email_check->bind_param("s", $user_ion_id);
        $email_check->execute();
        $result = $email_check->get_result(); // get the mysqli result
        $result->num_rows;
        if ($result->num_rows >0){
            $user=$result->fetch_assoc(); // fetch data 
            return $user;
        }else{
            return false;
        }
         // $db_conn->close();
    }
    public function find_user($email, $db_conn)
    {
        $email_check=$db_conn->prepare("SELECT * FROM secondary_user where email =?");
        $email_check->bind_param("s", $email);
        $email_check->execute();

        $result = $email_check->get_result(); // get the mysqli result
        if ($result->num_rows >0){
            $user = $result->fetch_assoc(); // fetch data 
            return $user;
            // $db_conn->close();
        }else{
            // $db_conn->close();
            return false; 
        }
    }
    
    public function find_otp($otp,$email, $db_conn)
    {
        $email_check=$db_conn->prepare("SELECT * FROM secondary_user where email =? and forgot_otp=?");
        $email_check->bind_param("si", $email,$otp);
        $email_check->execute();

        $result = $email_check->get_result(); // get the mysqli result
        if ($result->num_rows >0){
            $user = $result->fetch_assoc(); // fetch data 
            return $user;
            // $db_conn->close();
        }else{
            // $db_conn->close();
            return false; 
        }
    }

    public function set_otp($user_ion_id ,$db_conn){
        $otp=rand(1000,100000);
        $query=$db_conn->prepare("UPDATE secondary_user SET forgot_otp=? WHERE user_ion_id=?");
        $query->bind_param("is", $otp,$user_ion_id);
        $query->execute();
       if($query->affected_rows)
       {  
            return array("otp"=>$otp);  
       }
    }

    public function set_password($password,$email,$db_conn){
        $otp=rand(1000,100000);

        $query=$db_conn->prepare("UPDATE secondary_user SET password=? ,forgot_otp=NULL WHERE email=?");
        $query->bind_param("ss", $password,$email,);
        $query->execute();
       if($query->affected_rows > 0)
       {  
            return true;
       }else
            return false;
    }

    public function update_user($user_ion_id,$db_conn)
    {
        $user_name=$this->user_name;
        $email=$this->email;
        $password=$this->password;
        $first_name=$this->first_name;
        $last_name=$this->last_name;
        $phone_number=$this->phone_number;
        $address=$this->address;
        $profile_image=$this->profile_image;
        $email_sending=$this->email_sending;
        $cradit_recharge=$this->cradit_recharge;
        $updated_at=date("Y-m-d G:i:s");
        
        $query=$db_conn->prepare("UPDATE secondary_user SET user_name=?,email=?,password=?,first_name=?,last_name=?,phone_number=?,address=?,profile_image=?,email_sending=?,cradit_recharge=?,updated_at=? WHERE user_ion_id=?");
        $query->bind_param("sssssissiiss", $user_name,$email,$password,$first_name,$last_name,$phone_number,$address,$profile_image,$email_sending,$cradit_recharge,$updated_at,$user_ion_id);
        $query->execute();
        if ($query->affected_rows >0){
            return true;
        }
        else{
            return false;
        }
        $db_conn->close();
    }
    
}
