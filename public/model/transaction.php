<?php
  class Transaction {
    
    private $transaction_id;
    private $customer_id;
    private $merchant_id;
    private $product;
    private $amount;
    private $currency;
    private $status;

    public function set_transaction($transaction_id,$customer_id,$merchant_id,$product,$amount,$currency,$status){
      $this->transaction_id=$transaction_id;
      $this->customer_id=$customer_id;
      $this->merchant_id=$merchant_id;
      $this->product=$product;
      $this->amount=$amount;
      $this->currency=$currency;
      $this->status=$status;
    }

    public function addTransaction($db_conn) {

      $stmt=$db_conn->prepare('INSERT INTO transaction (transaction_id, 
        customer_id,merchant_id, product, amount, currency, status) VALUES(?,?,?,?,?,?,?)');
      $stmt->bind_param('ssssiss', $this->transaction_id,$this->customer_id,$this->merchant_id,$this->product,$this->amount,$this->currency,$this->status);

      if($stmt->execute()) {
        return true;
      } else {
        return false;
      }
    }
}