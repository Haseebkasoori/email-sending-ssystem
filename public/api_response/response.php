<?php
//DO NOT MANIPULATE THIS CLASS
class Response
{
    private $message;
    private $key;
    private $data;
    private $description;
    private $http_status_code;

    //setting error response properties
    public function set_error_response($data, $message, $key, $description)
    {
        $this->data = $data;
        $this->message = $message;
        $this->key = $key;
        $this->description = $description;
        $this->http_status_code = $key;
    }
    //setting error response properties
    public function set_success_response($data, $message, $key, $description)
    {
        $this->data = $data;
        $this->message = $message;
        $this->key = $key;
        $this->description = $description;
        $this->http_status_code = $key;
    }
    //getting response as associative array
    public function get_error_response_assoc()
    {
        $response = array(
            "data" => $this->data,
            "error"=>array("message" => $this->message,
                            "key" => $this->key,
                        ),
            "description" => $this->description
        );
        http_response_code($this->key);
        return $response;
    }
    public function get_success_response_assoc()
    {
        $response = array(
            "data" => $this->data,
            "success"=>array("message" => $this->message,
                            "key" => $this->key,
                        ),
            "description" => $this->description
        );
        http_response_code($this->key);
        return $response;
    }
    
    //getting response as associative array and save that in database
    public function save_respond_request($request_id,$db_conn)
    {
        $query = "INSERT INTO request_response (request_id,response_message,key,data,description) VALUES ('$request_id',$this->message, $this->key, $this->data, $this->description)";
            $result = $db_conn->query($query);
            if($result)
            {
                return true;
            }
            else{
                return false;
            }
             $db_conn->close();
    }
    public function error_respond_api()
    {
        echo json_encode($this->get_error_response_assoc());
    }
    public function success_respond_api()
    {
        echo json_encode($this->get_success_response_assoc());
    }
}
