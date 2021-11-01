<?php
header("Access-Control-Allow-origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type:application/json; charset=utf-8");

require_once '../../../config/connection/connection.php';
require_once '../../model/user/user.php';
require_once '../../api_response/response.php';
include_once '../../helpers/validator.php';
require_once '../../../vender/autoload.php';

use\Firebase\JWT\JWT;

$response= new Response();
if ($_SERVER['REQUEST_METHOD']=== "POST") {


        $secret_key="P0551BL3"
        $iss = "localhost";
        $iat = time() ; 
        $nbf = $iat+10; 
        $exp = $iat+180; 
        $aud = "Admin"; 
        $user_data_array = array(
            $user_ion_id = $user->user_ion_id;
            $user_name = $user->username;
            $email = $user->email;
            $passwprd = $user->password;
        );,

        $payload_info= array(
            "iss" =>$iss  ,
            "iat" =>$iat  , 
            "nbf" =>$nbf  ,
            "exp" =>$exp  , 
            "aud" =>$aud  , 
            "data" =>$user_data_array
            );, 

        );
          $Auth_key = JWT::encode($payload_info,$secret_key);

        // on recieving end
          $all_headers=getallheaders();
          $data-jwt=$all_headers['Auth_key'];

}

// //create response class object
// $res = new Response();

// //get the data from postman
// $data = json_decode(file_get_contents("php://input"),true);
// $email=$data['email'];
// $password=$data['password'];

// //perform validation on email format
// if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
// {
//      $res->set_response(null,'invalid email format',409);
//      $res->respond_api();
// }
// else{
//      // create object of database class
//      $database = new Database();
//      // get database connection
//      $db = $database->get_Connection();

//      // create user object
//      $user = new User();

//      // call sign_in() method and pass required parameters 
//      $result = $user->sign_in($email,$password,$db);

//           if($result->num_rows > 0){    
//                $row = $result->fetch_assoc();
//                $res->set_response(null,"Successfully Login!",200);
//                $res->respond_api();
//           }
//           else{
//           $user_arr=array(
//                "status" => 406,
//                "message" => "Invalid Username or Password!",
//           );
//           $res->set_response(null,$user_arr['message'],$user_arr['status']);
//           $res->respond_api();
//           }
//      }
 ?>
