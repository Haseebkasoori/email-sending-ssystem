<?php
ini_set("display_error",1);
header("Access-Control-Allow-origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type:application/json; charset=utf-8");

require_once '../../../config/connection/connection.php';
require_once '../../../vendor/autoload.php'; 
require_once '../../model/user/user.php';
require_once '../../api_response/response.php';
include_once '../../helpers/validator.php';
include_once '../../helpers/mail.php';
include_once '../../language/language.php';

$response= new Response();
if ($_SERVER['REQUEST_METHOD']=== "POST") {

   $data = json_decode(file_get_contents('php://input'));

   $validator = new Validator();

   //validating passwrod format & generating respective responses
   if (!empty($data->password) && !empty($data->confirm_password)) {
      if ($data->password === $data->confirm_password) {
         if($validator->validate_password($data->password) === false) {
              $response->set_error_response(null,"invalid password format", "400","Password should be at least 8 characters in length and should include set_error_response least one upper case letter, one number, and one special character");
              echo $response->error_respond_api();
         }
      }else{
            $response->set_error_response(null, "Password and confirm password not match", "400","passwrod and confirm password not match");
             echo $response->error_respond_api();
         }
   }else{
      $response->set_error_response(null, "Password and confirm password required", "400","Password and confirm password field empty please fill these");
       echo $response->error_respond_api();
   }
   
   //if data valid, create database object, get connection
   $db=new DataBase();
   $conn = $db->get_connection();
   $user = new User();
   $mail = new Mail();
   // $email = new \SendGrid\Mail\Mail();
   
   $user_data =$user->find_otp("merchants",$data->otp,$data->email,$conn);
   if ($user_data===true) {
      
      $password= password_hash($data->password, PASSWORD_DEFAULT);
      
      // update password in database
      $set_password=$user->update_password("merchants",$password,$data->email,$conn);
      if (!empty($set_password)) {
         try{            
            $mail->reset_password_mail($user_data['email'],$user_data['user_name'],$user_data['user_ion_id'],$data->password);
            $response->set_success_response(null, "Password save successfully", "200","please check your mail for Key and password for login");
             echo $response->success_respond_api();
         }catch (Exception $ex) {
            $response->set_error_response(null, $ex->getMessage(), "500","Somthing went worng");
            echo $response->error_respond_api();
         }
      }
   }else{
      $response->set_error_response(null, "Expire OTP", "400","This OTP already used or expire, please create another one");
      echo $response->error_respond_api();
   }
}
