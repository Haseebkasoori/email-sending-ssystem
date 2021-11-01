<?php
class Merchant
{
    private $id;
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

    // set properties of class
    public function set_user($user_name,$email,$password,$first_name,$last_name,$phone_number,$address,$user_ion_id,$profile_image)
    {
        $this->user_name    = $user_name;
        $this->email        = $email;
        $this->password     = $password;
        $this->first_name   = $first_name;
        $this->last_name    = $last_name;
        $this->phone_number = $phone_number;
        $this->address      = $address;
        $this->user_ion_id  = $user_ion_id;        
        $this->profile_image  = $profile_image;        
    }
    // getting properties of class
    public function get_user()
    {
        $user = array(
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
            "profile_image" => $this->$profile_image
        );
        return $user;
    }
    // create new user
    public function sign_up($db_conn)
    {
        $query="INSERT INTO merchants (user_name,email,password,first_name,last_name,phone_number,address,user_ion_id,profile_image) VALUES (?,?,?,?,?,?,?,?,?)";
        $query=$db_conn->prepare($query);
        $query->bind_param("sssssisss", $this->user_name,$this->email,$this->password,$this->first_name,$this->last_name,$this->phone_number,$this->address,$this->user_ion_id,$this->profile_image);
        $query->execute();
        if ($query->affected_rows >0){
            return true;
        }
        else{
            return false;
        }
        $db_conn->close();
    }
    // add cradit amount 
    public function add_credit($user_ion_id,$day_limit, $db_conn)
    {
        $query="INSERT INTO user_credit (merchant_ion_id,day_limit) VALUES (?,?)";
        
        $query=$db_conn->prepare($query);
        $query->bind_param("ss", $user_ion_id,$day_limit);
        $query->execute();
        print_r($query);
        exit;
        if ($query->affected_rows >0){
            return true;
        }
        else{
            return false;
        }
        $db_conn->close();       
    }
    // check users for login
    public function check_user($user_ion_id,$db_conn)
    {
      //create query
        $email_check = $db_conn->prepare("SELECT * FROM merchants WHERE user_ion_id=?");
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
         $db_conn->close();
    }
    
    
    // get user cradit
    public function get_user_cradit($merchant_ion_id, $db_conn)
    {
        $today=date('Y-m-d G:i:s');
        $user_credit=$db_conn->prepare("SELECT * FROM user_credit where merchant_ion_id =? and day_limit> ?");
        $user_credit->bind_param("ss", $merchant_ion_id,$today);
        $user_credit->execute();
        $result = $user_credit->get_result(); // get the mysqli result
        if ($result->num_rows >0){
            $user_credit = $result->fetch_assoc(); // fetch data 
            return $user_credit;
        }else{
            return false; 
        }
            $db_conn->close();
    }
    // add charge amount for every email
    public function charge_amount($merchant_ion_id,$remaining_cradit,$db_conn)
    {
        $updated_at=date('Y-m-d G:i:s');
        $user_credit=$db_conn->prepare("UPDATE user_credit SET credit=?,updated_at=? WHERE merchant_ion_id=?");
        $user_credit->bind_param("dss",$remaining_cradit,$updated_at,$merchant_ion_id);
        $user_credit->execute();
        if ($user_credit->affected_rows >0){
            return true;
        }else{
            return false; 
        }
            $db_conn->close();
    }
    // get cradit card data
    public function find_cradit_card($card_number,$merchant_id,$db_conn)
    {   
        $sql="SELECT * FROM user_cradit_card where card_number =? and merchant_ion_id=?";
        $statment=$db_conn->prepare($sql);
        $statment->bind_param("si", $card_number,$merchant_id);
        $statment->execute();
        $result = $statment->get_result(); // get the mysqli result
        if ($result->num_rows >0){
            $card = $result->fetch_assoc(); // fetch data 
            return $card;
        }else{
            return false; 
        }
            $db_conn->close();
    }




    // update credit after 
    public function update_cradit($merchant_ion_id,$db_conn)
    {
        $day_limit=date('Y-m-d G:i:s', strtotime('+60 days'));
        $updated_at=date('Y-m-d G:i:s');
        $cradit_amount=100;
        $user_credit=$db_conn->prepare("UPDATE user_credit SET credit=?,day_limit=?,updated_at=? WHERE merchant_ion_id=?");
        $user_credit->bind_param("dsss",$cradit_amount,$day_limit,$updated_at,$merchant_ion_id);
        $user_credit->execute();
        if ($user_credit->affected_rows >0){
            return true;
        }else{
            return false; 
        }
            $db_conn->close();
    }
    // update customer id in merchant id
    public function update_stripe_id($stripe_customer_id,$merchant_ion_id, $db_conn)
    {
        $updated_at=date('Y-m-d G:i:s');
        $user_credit=$db_conn->prepare("UPDATE merchants SET stripe_customer_id=?,updated_at=? WHERE user_ion_id=?");
        $user_credit->bind_param("sss",$stripe_customer_id,$updated_at,$merchant_ion_id);
        $user_credit->execute();
        if ($user_credit->affected_rows >0){
            return true;
        }else{
            return false; 
        }
            $db_conn->close();
    }
    // save cutomer cradit card for future reuse
    public function add_card($merchant_id,$number,$exp_month,$exp_year,$cvc,$customer_id,$db_conn)
    {
        
        $query=$db_conn->prepare("INSERT INTO user_cradit_card (merchant_ion_id,card_number,exp_month,exp_year,cvc,stripe_customer_id) VALUES (?,?,?,?,?,?)");
        $query->bind_param("siiiis", $merchant_id,$number,$exp_month,$exp_year,$cvc,$customer_id);
        $query->execute();
        
        if ($query->affected_rows >0){
            return true;
        }
        else{
            return false;
        }
        $db_conn->close();       

    }
    // get all users data
    public function get_all_users($merchant_ion_id, $db_conn)
    {

        $user_data=$db_conn->prepare("SELECT * FROM merchants");
        $user_data->execute();

        print_r($user_data);
        exit;
        $result = $user_data->get_result(); // get the mysqli result
        if ($result->num_rows >0){
            $user_data = $result->fetch_assoc(); // fetch data 
            return $user_data;
        }else{
            return false; 
        }
            $db_conn->close();
    }  
}
