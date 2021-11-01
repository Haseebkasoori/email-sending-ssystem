<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-type:application/json; charset=utf-8");

require_once '../../../config/connection/connection.php';
require_once '../../../vendor/autoload.php'; 
require_once '../../model/user/user.php';
require_once '../../model/user/merchant.php';
require_once '../../model/transaction.php';
require_once '../../api_response/response.php';
include_once '../../helpers/validator.php';
include_once '../../helpers/varification.php';
include_once '../../helpers/mail.php';
include_once '../../language/language.php';

\Stripe\Stripe::setApiKey('sk_test_51HfMAJKav7v2b6Ix55oZIL4qloYEYvZ4O0neZqoALRQiqGtvM5N6xjgYu3BmO5VL38g7bYVn8USOtnHsEHAY3Fwy00zs1zXo4Z');


if ($_SERVER['REQUEST_METHOD']=== "POST") {

    $data = json_decode(file_get_contents('php://input'));
        // object creating
        $db=new DataBase();
        $conn = $db->get_connection();
        $mercharnt = new Merchant();
        $user = new User();
        $validator = new Validator();
        $varification = new Varification();
        $mail = new Mail();
        $response= new Response();
        // createing strip client object
        $stripe = new \Stripe\StripeClient('sk_test_51HfMAJKav7v2b6Ix55oZIL4qloYEYvZ4O0neZqoALRQiqGtvM5N6xjgYu3BmO5VL38g7bYVn8USOtnHsEHAY3Fwy00zs1zXo4Z');

        // Get Auth key for varification
        $all_headers=getallheaders();
        $jwt_key=$all_headers['Auth_key'];
        if (!empty($jwt_key)) {
            if (!empty($data->merchant_id)) {
              
                $get_user=$user->find_user("merchants","user_ion_id",$data->merchant_id,$conn); 
                if(!empty($get_user)){
                  try{  
                    $varify=$varification->get_user_data($jwt_key);
                    if(!empty($varify['decoded_data'])){
                        $merchant_id=$data->merchant_id;

                        // check database if user exist in cradit card so skip the customer creation
                        
                        $card_data=$mercharnt->find_cradit_card($data->card_number,$data->merchant_id,$conn);
                        $charge_success=false;
                        if ($card_data) {
                            $customer_id=$card_data['stripe_customer_id'];
                            
                            // Charge payment
                            $charge = \Stripe\Charge::create(array(
                              "amount" => 10000,
                              "currency" => "usd",
                              "description" => "ESS Recharge Payment",
                              "customer" => $customer_id
                            ));

                            $charge_success=true;
                        }else{
                            // creting the strip card token
                            $token=$stripe->tokens->create([
                              'card' => [
                                'number' => $data->card_number,
                                'exp_month' => $data->exp_month,
                                'exp_year' => $data->exp_year,
                                'cvc' => $data->cvc,
                              ],
                            ]);
                            
                            // Create Customer In Stripe
                            $customer = \Stripe\Customer::create(array(
                              "email" => $varify['decoded_data']->data->email,
                              "phone" => $varify['decoded_data']->data->phone_number,
                              "name" => $varify['decoded_data']->data->user_name,
                              "description" => "My this my ESS customer from".$varify['decoded_data']->data->address,
                              "source" => $token->id
                            ));
                            // Charge payment
                            $charge = \Stripe\Charge::create(array(
                              "amount" => 10000,
                              "currency" => "usd",
                              "description" => "ESS Recharge Payment",
                              "customer" => $customer->id
                            ));
                            $charge_success=true;

                            $mercharnt->add_card($data->merchant_id,$data->card_number,$data->exp_month,$data->exp_year,$data->cvc,$customer->id,$conn);
                        }

                        if ($charge_success) {

                          // update cradit of user in database
                          $mercharnt->update_cradit($data->merchant_id,$conn);

                          // save transaction properties in database
                          $transaction_id = $charge->id;
                          $customer_id = $charge->customer;
                          $merchant_id = $data->merchant_id;
                          $product = $charge->description;
                          $amount = $charge->amount;
                          $currency = $charge->currency;
                          $status = $charge->status;
                        
                          // Instantiate Transaction
                          $transaction = new Transaction();

                          // Add Transaction To DB
                          $transaction->set_transaction($transaction_id,$customer_id,$merchant_id,$product,$amount,$currency,$status);
                          $transaction->addTransaction($conn);
                        
                          //Set response properties
                          $response->set_success_response(null, "successfully Update cradit", "200","you have successfully update your cradit now you send you mails for next 60 days");
                          // set and print the response
                          http_response_code(200);
                          echo $response->success_respond_api();

                        }                      

                    }else{
                        $response->set_error_response(null,$varify['error'], "500","Invaid Auth key please create another one");
                        http_response_code(500);
                        echo $response->error_respond_api();
                    }
                  }catch (Exception $ex) {
                    $response->set_error_response(null,$ex->getMessage(), "500","something went worng");
                    http_response_code(500);
                    echo $response->error_respond_api();
                  } 
                }else{
                    $response->set_error_response(null,"Invalid user id", "404","Please enter a valid id");
                    http_response_code(404);
                    echo $response->error_respond_api();
                }
            }else{
                    $response->set_error_response(null,"user id required", "404","user id field empty");
                    http_response_code(404);
                    echo $response->error_respond_api();
                }
        }else{
            $response->set_error_response(null,"Auth key required", "404","Auth key required");
            http_response_code(404);
            echo $response->error_respond_api();
        }
}

