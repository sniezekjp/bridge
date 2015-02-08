<?php

require_once('lib/BasePostType.php');

class Payment extends BasePostType {
  
  public static $type = "payment";

  public $attributes = array(
    'id', 'amount', 'status', 'type', 'contactId', 'fee', 'txn_id'
  );

  public function validate($model) {
    $model = (array) $model;
    $errors = array();

    if(!$model['amount'] || $model['amount'] === '') {
      $errors['amount'] = true;
    }
  
    if(count($errors) >= 1) {
      $response = array();
      $response['success'] = false;
      $response['errors'] = $errors;
      echo json_encode($response);
      exit;
    }
    return true;
  }
  
  public function transformRequest($data) {
    $data = (object) $data;
    $className = get_class($this);
    $transformed = array();
    $transformed['post'] = array(
      'ID' => $data->id ? $data->id : null,
      'post_title' => $data->firstName . ' ' . $data->lastName,
      'post_type' => $className::$type,
      'post_status' => 'publish'
    );
    $transformed['meta'] = array();
    foreach($this->attributes as $attr) {
      $transformed['meta'][$attr] = $data->$attr;
    }
    return $transformed;
  }

  public function transformResponse($post, $meta) {
    $data = array();
    $data['id']        = (int) $post->ID;
    $data['amount']    = $meta['amount'][0];
    $data['fee']       = $meta['fee'][0];
    $data['txn_id']    = $meta['txn_id'][0];
    $data['status']    = $meta['status'][0];
    $data['type']      = $meta['type'][0];
    $data['contactId'] = (int) $meta['contactId'][0];
    $data['createdAt'] = $post->post_date;
    return $data;
  }

  public function notify() {
    header("HTTP/1.1 200 OK");
    $raw_post_data = file_get_contents('php://input');
    $raw_post_array = explode('&', $raw_post_data);
    $myPost = array();
    foreach ($raw_post_array as $keyval) {
      $keyval = explode ('=', $keyval);
      if (count($keyval) == 2)
         $myPost[$keyval[0]] = urldecode($keyval[1]);
    }
    // read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
    $req = 'cmd=_notify-validate';
    if(function_exists('get_magic_quotes_gpc')) {
       $get_magic_quotes_exists = true;
    } 
    foreach ($myPost as $key => $value) {        
       if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) { 
            $value = urlencode(stripslashes($value)); 
       } else {
            $value = urlencode($value);
       }
       $req .= "&$key=$value";
    }

    $ch = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
    curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cert.pem');

    if( !($res = curl_exec($ch)) ) {
        file_put_contents("paypal-error.txt", "Got " . curl_error($ch) . " when processing IPN data");
        curl_close($ch);
        exit;
    }
    curl_close($ch);

    if (strcmp ($res, "VERIFIED") == 0) {
      file_put_contents('post-data.txt', json_encode($_POST, JSON_PRETTY_PRINT));
      $payment = array();
      $payment['id']        = (int) $_POST['invoice'];
      $payment['fee']       = $_POST['mc_fee'];
      $payment['amount']    = $_POST['mc_gross'];
      $payment['status']    = $_POST['payment_status'];
      $payment['txn_id']    = $_POST['txn_id'];
      $payment['type']      = 'paypal';
      $payment['contactId'] = (int) $_POST['custom'];
      $payment['createdAt'] = $post->post_date;
      $this->update($payment);
      exit;
    } else if (strcmp ($res, "INVALID") == 0) {
      file_put_contents("post-data.txt", "INVALID");
      exit;
    }
  }
}
