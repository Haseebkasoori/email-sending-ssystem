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
include_once '../../helpers/varification.php';
include_once '../../language/language.php';

$response= new Response();
if ($_SERVER['REQUEST_METHOD']=== "POST") {

    $data = json_decode(file_get_contents('php://input'));
        
    // object creating
    $db=new DataBase();
    $conn = $db->get_connection();
    $user = new User();
    $varification = new Varification();

    // Get Auth key for varification
    $all_headers=getallheaders();
    $jwt_key=$all_headers['Auth_key'];
    if (!empty($jwt_key)) {
        try{
            $varify=$varification->get_user_data($jwt_key);
            if(!empty($varify['decoded_data'])){
                
                // get all Merchant users
                $all_users_data=$user->get_all_merchants_users($varify['decoded_data']->data->user_ion_id,$conn);
                
                // unset($all_users_data['id']);
                // show response
                $response->set_success_response($all_users_data, "All Secondary User Data", "200","Secondary User Data");
                echo $response->success_respond_api($conn);
            }else{
                $response->set_error_response(null,$varify['error'], "500","Invaid Auth key please create another one");
                echo $response->error_respond_api();
            }            
        }catch(Exception $ex){
                $response->set_error_response(null,$ex->getMessage(), "500","Something went worng");
                echo $response->error_respond_api();
        }
    }else{
        $response->set_error_response(null,"Auth key required", "404","Auth key required");
        echo $response->error_respond_api();
    }
}

