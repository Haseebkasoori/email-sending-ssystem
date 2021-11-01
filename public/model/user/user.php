<?php
class User
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
    private $merchant_id;

    // set properties of class
    public function set_user($user_name="",$email="",$password="",$first_name="",$last_name="",$phone_number="",$address="",$user_ion_id="",$profile_image="",$email_sending="",$cradit_recharge="",$merchant_id="")
    {
        $this->user_name            = $user_name;
        $this->email                = $email;
        $this->password             = $password;
        $this->first_name           = $first_name;
        $this->last_name            = $last_name;
        $this->phone_number         = $phone_number;
        $this->address              = $address;
        $this->user_ion_id          = $user_ion_id;        
        $this->profile_image        = $profile_image;        
        $this->email_sending        = $email_sending;        
        $this->cradit_recharge      = $cradit_recharge;        
        $this->merchant_id          = $merchant_id;        
    }
    // getting properties of class
    public function get_user()
    {
        $user = array(
            "user_name" => $this->user_name,
            "email" => $this->email,
            "password" => $this->password,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "phone_number" => $this->phone_number,
            "address" => $this->address,
            "user_ion_id" => $this->user_ion_id,
            "stripe_customer_id" => $this->stripe_customer_id,
            "forgot_otp" => $this->forgot_otp,
            "profile_image" => $this->profile_image,
            "email_sending" => $this->email_sending,
            "cradit_recharge" => $this->cradit_recharge,
            "merchant_id" => $this->merchant_id,
        );
        return $user;
    }
    
    // fine user data
    public function find_user($table,$column,$value, $db_conn)
    {
        if ( in_array( $table, ['admin','merchants','secondary_user'] ) AND in_array( $column, ['email','user_ion_id'] )) 
            { 
                $sql = "SELECT * From {$table} WHERE {$column}=?"; 
            }else{
                return false;
            }
        $statment=$db_conn->prepare($sql);
        $statment->bind_param("s", $value);
        $statment->execute();
        $result = $statment->get_result(); // get the mysqli result
        if ($result->num_rows >0){
            $user = $result->fetch_assoc(); // fetch data 
            return $user;
        }else{
            return false; 
        }
            $db_conn->close();
    }
    // fine Get all data
    public function get_all_users($table,$db_conn)
    {
        if ( in_array( $table, ['admin','merchants','secondary_user'] ) ) 
            { 
                $sql = "SELECT * From {$table}"; 
                if ($table==="secondary_user") {
                    $sql="SELECT secondary_user.*,merchants.id as merchants_id,merchants.user_name,user_credit.id as payed_id, user_credit.day_limit,user_credit.credit, user_credit.updated_at as cradit_update FROM secondary_user LEFT JOIN merchants on merchants.user_ion_id= merchants.id LEFT JOIN user_credit on user_credit.merchant_ion_id=merchants.user_ion_id";
                }else if($table==="merchants"){
                    $sql="SELECT merchants.*,user_credit.id as payed_id, user_credit.day_limit,user_credit.credit, user_credit.updated_at as cradit_update FROM merchants LEFT JOIN user_credit on user_credit.merchant_ion_id= merchants.id";
                }
            }else{
                return false;
            }
        $statment=$db_conn->prepare($sql);
        $statment->execute();
        $result = $statment->get_result(); // get the mysqli result

        if ($result->num_rows >0){
            while ($row = $result->fetch_assoc()) {
                $user[]=$row;
            }
            // $user = $result->fetch_assoc(); // fetch data 
            return $user;
        }else{
            return false; 
        }
            $db_conn->close();
    }
    // fine Get all data
    public function get_all_merchants_users($merchant_id,$db_conn)
    {
        $sql="SELECT secondary_user.*,merchants.id as merchants_id,merchants.user_name FROM secondary_user LEFT JOIN merchants on merchants.user_ion_id= merchants.id where secondary_user.merchant_id='{$merchant_id}'";
        
        $statment=$db_conn->prepare($sql);
        $statment->execute();
        $result = $statment->get_result(); // get the mysqli result

        if ($result->num_rows >0){
            while ($row = $result->fetch_assoc()) {
                $user[]=$row;
            }
            // $user = $result->fetch_assoc(); // fetch data 
            return $user;
        }else{
            return false; 
        }
            $db_conn->close();
    }

    public function sign_up($table, $db_conn)
    {
        
        if ( in_array( $table, ['admin','merchants','secondary_user'] )) 
        { 
            if ($table==='secondary_user') {

                $sql = "INSERT INTO {$table} (user_name,email,password,first_name,last_name,phone_number,address,user_ion_id,profile_image,email_sending,cradit_recharge,merchant_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"; 

            $query=$db_conn->prepare($sql);
            $query->bind_param("sssssisssiis", $this->user_name,$this->email,$this->password,$this->first_name,$this->last_name,$this->phone_number,$this->address,$this->user_ion_id,$this->profile_image,$this->email_sending,$this->cradit_recharge,$this->merchant_id);
            }else{

                $sql = "INSERT INTO {$table} (user_name,email,password,first_name,last_name,phone_number,address,user_ion_id,profile_image) VALUES (?,?,?,?,?,?,?,?,?)"; 

                $query=$db_conn->prepare($sql);
                $query->bind_param("sssssisss", $this->user_name,$this->email,$this->password,$this->first_name,$this->last_name,$this->phone_number,$this->address,$this->user_ion_id,$this->profile_image);
            }
        }

            $query->execute();
            if ($query->affected_rows >0){

                return true;
            }
            else{
                return false;
            }
         $db_conn->close();
    }

    public function update_profile($table, $db_conn)
    {
        if ( in_array( $table, ['admin','merchants','secondary_user'] )) 
        { 
            $now=date('Y-m-d G:i:s');
            if ($table==='admin') {

                $sql = "UPDATE {$table} SET user_name='{$this->user_name}',first_name='{$this->first_name}',last_name='{$this->last_name}',phone_number='{$this->phone_number}',address='{$this->address}',profile_image='{$this->profile_image}',updated_at='{$now}' WHERE user_ion_id='{$this->user_ion_id}'";
            }elseif($table==='merchants'){
                
                $sql = "UPDATE {$table} SET user_name='{$this->user_name}',email='{$this->email}',password='{$this->password}',first_name='{$this->first_name}',last_name='{$this->last_name}',phone_number='{$this->phone_number}',address='{$this->address}',profile_image='{$this->profile_image}' ,updated_at='{$now}' WHERE user_ion_id='{$this->user_ion_id}'";
            }elseif ($table==='secondary_user') {
                $sql = "UPDATE {$table} SET user_name='{$this->user_name}',email='{$this->email}',first_name='{$this->first_name}',last_name='{$this->last_name}',phone_number='{$this->phone_number}',address='{$this->address}',profile_image='{$this->profile_image}',email_sending='{$this->email_sending}',cradit_recharge='{$this->cradit_recharge}',updated_at='{$now}' WHERE user_ion_id='{$this->user_ion_id}' and merchant_id='{$this->merchant_id}'";
            }
            $db_conn->query($sql);
            return true;

        }else{
            return false;
        }
        $db_conn->close();
    }

    public function set_otp($table, $user_ion_id ,$db_conn)
    {
        $otp=rand(1000,100000);
        
        if ( in_array( $table, ['admin','merchants','secondary_user'] )) 
        { 
            $sql = "UPDATE {$table} SET forgot_otp=? where user_ion_id=?"; 
        }else{
            return false;
        }
        
        $query=$db_conn->prepare($sql);
        $query->bind_param("is", $otp,$user_ion_id);
        $query->execute();
       if($query->affected_rows)
       {  
            return $otp;  
       }
       $db_conn->close();
    }

     // get otp for password changing
    public function find_otp($table,$otp,$email,$db_conn)
    {   
        if ( in_array( $table, ['admin','merchants','secondary_user'] )) 
        { 
            $sql="SELECT * FROM {$table} where email =? and forgot_otp=?";
            
            $statment=$db_conn->prepare($sql);
            $statment->bind_param("si", $email,$otp);
            $statment->execute();

            $result = $statment->get_result(); // get the mysqli result
            if ($result->num_rows >0){
                $user = $result->fetch_assoc(); // fetch data 
                return $user;
            }else{
                return false; 
            }
        }else{
            return false;
        }
            $db_conn->close();
    }

    public function update_password($table,$password,$email,$db_conn)
    {
        if ( in_array( $table, ['admin','merchants','secondary_user'] )) 
        { 
            $sql = "UPDATE {$table} SET password=?,forgot_otp=null where email=?"; 
        }else{
            return false;
        }
        
        $query=$db_conn->prepare($sql);
        $query->bind_param("ss", $password,$email);
        $query->execute();
       if($query->affected_rows){
            return true;
       }else
            return false;
        $db_conn->close();
    }

    // save iamge into server
    public function save_image($user_type,$image_data){

        if (!empty($image_data)) {
            
               $imageData = base64_decode($image_data['image']);
               $extension = $image_data['extension'];
               $filepath="../uploads/{$user_type}/". uniqid().".%s";
               $filename = sprintf($filepath, $extension);
               if(file_put_contents($filename, $imageData))
               {
                $profile_image=$filename;
                return $profile_image; 
               }else{
                return false;
               }
        }
    }


    // // sign in function
    // public function sign_in($table,$column,$value , $password, $db_conn)
    // {
    //     if ( in_array( $table, ['admin','merchants','secondary_user'] ) AND in_array( $column, ['email','user_ion_id'] )) 
    //         { 
    //             $sql = "UPDATE {$table} SET auth_key=?  WHERE {$column}=?"; 
    //         }else{
    //             return false;
    //         }
    //   //create query
    //   $query = "";
    //     $result = $db_conn->query($query);
    //     if($result)
    //     {
    //         return $result;
    //     }else{
    //         return false;
    //     }
    //      $db_conn->close();
    // }
        
    
     // // Update Auth key in table
     //    public function set_auth_key($table,$column,$value,$auth_key,$db_conn)
     //    {
     
     //        if ( in_array( $table, ['admin','merchants','secondary_user'] ) AND in_array( $column, ['email','user_ion_id'] )) 
     //                { 
     //                    $sql = "UPDATE {$table} SET auth_key=?  WHERE {$column}=?"; 
     //                }else{
     //                    return false;
     //                }

     //        $query=$db_conn->prepare($sql);
     //        $query->bind_param("ss",$auth_key,$value);
     //        $query->execute();
     //       if($query->affected_rows)
     //       {  
     //            return True;  
     //       }else{
     //            return false;  
     //       }
     //       $db_conn->close();
     //    }
        
    }
