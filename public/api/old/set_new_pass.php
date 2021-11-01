<?php
header("Access-Control-Allow-origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type:application/json; charset=utf-8");

require_once '../../../config/connection/connection.php';
require_once '../../model/user/user.php';
require_once '../../api_response/response.php';
include_once '../../helpers/validator.php';


$response= new Response();
if ($_SERVER['REQUEST_METHOD']=== "POST") {


}


   // $data = json_decode(file_get_contents('php://input'));
   //  $res = new Response();
   //  $token = $data->token;
   //  $new_pass = $data->new_pass;
   //  $confirm_pass = $data->confirm_pass;

   // if($new_pass === $confirm_pass)
   // {
   //  $db=new DataBase();
   //  $conn = $db->get_connection();
   //  $user = new User();
   //  $boolean = $user->set_new_pass($new_pass, $confirm_pass, $token, $conn);
     
   //  if($boolean === true)
   //  {
   //      $message = "Pass changed successfully";
   //      $status_code = "201";
   //      $res->set_response(null, $message, $status_code);
   //      http_response_code(201);
   //      $res->respond_api();
   //      $db->close_connection();
   //  }
   //  else if($boolean === false)
   //  {
   //      $message = "Invalid Credential";
   //      $status_code = "406";
   //      $res->set_response(null, $message, $status_code);
   //      $res->respond_api();
   //      $db->close_connection();
   //  }
   // }
   // else
   // {
   //  $message = "password field does not match";
   //  $status_code = "401";
   //  $res->set_response(null, $message, $status_code);
   //  $res->respond_api();
   //  $db->close_connection();
   // }

