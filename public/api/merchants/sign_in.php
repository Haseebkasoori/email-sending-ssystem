<?php
ini_set("display_error",1);

header("Access-Control-Allow-origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type:application/json; charset=utf-8");

require_once '../../../config/connection/connection.php'; 
require_once '../../model/user/user.php';
require_once '../../model/user/merchant.php';
require_once '../../api_response/response.php';
include_once '../../helpers/validator.php';
include_once '../../helpers/varification.php';
include_once '../../language/language.php';


$response= new Response();
if ($_SERVER['REQUEST_METHOD']=== "POST") {

    $data = json_decode(file_get_contents('php://input'));
    if (!empty($data->email) or !empty($data->user_id)) {

        if (!empty($data->password)) {

            $db=new DataBase();
            $conn = $db->get_connection();
            $user = new User();
            $merchants = new Merchant();
            $varification = new Varification();
            if (isset($data->user_id)) {
                $get_user=$user->find_user("merchants",'user_ion_id',$data->user_id,$conn);
            }else if (isset($data->email)) {
                    $get_user=$user->find_user("merchants",'email',$data->email,$conn);

                    if ($validator->validate_email($data->email) === True) 
                    {
                        $response->set_error_response(null,"invalid email format", "404","Please type proper email address");
                        echo $response->error_respond_api();    
                    }
                    
                }
                
                if($get_user){
                    if(password_verify($data->password, $get_user['password'])){
                        
                        $get_user['password']=$data->password;
                        // create token for varification
                        $create_varification=$varification->set_key($get_user);

                        if(!empty($create_varification['Auth_key'])){

                            $response_data=array('Auth_key'=>$create_varification['Auth_key']);
                            $response->set_success_response($response_data, "Login successfully", "200","Use this Auth key in your Header for making request");
                            echo $response->success_respond_api();
                            
                        }else{

                            $response->set_error_response(null, "Server problem", "500",$create_varification['error']);
                            echo $response->error_respond_api();
                        }
                    }else{
                        $response->set_error_response(null, "Incorrect Password", "400","Password not match");
                        echo $response->error_respond_api();
                    }
                }else{

                    $response->set_error_response(null,"No User found", "404","Given data not register please register your self");
                    echo $response->error_respond_api();
                }
        }else{
            $response->set_error_response(null, "Password required", "400","Password field empty");
            echo $response->error_respond_api();
        }
    }else{
        $response->set_error_response(null, "user id required", "400","User id field empty");
        echo $response->error_respond_api();
    }

        // on recieving end
        // $all_headers=getallheaders();
        // $data-jwt=$all_headers['Auth_key'];

}

