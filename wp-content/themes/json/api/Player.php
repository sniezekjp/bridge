<?php

require_once('lib/BasePostType.php');

class Player extends BasePostType {
  public static $type = "player";

  public $attributes = array(
    'id', 'firstName', 'lastName', 'size', 'medical', 'contactId', 'paymentId', 'campId'
  );

  public function validate($model) {
    $model = (array) $model;
    $errors = array();
    if(!$model['firstName'] || $model['firstName'] === '') {
      $errors['firstName'] = true;
    }
    if(!$model['lastName'] || $model['lastName'] === '') {
      $errors['lastName'] = true; 
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
    $data['firstName'] = $meta['firstName'][0];
    $data['lastName']  = $meta['lastName'][0];
    $data['size']      = $meta['size'][0];
    $data['medical']   = $meta['medical'][0];
    $data['campId']    = (int) $meta['campId'][0];
    $data['contactId'] = (int) $meta['contactId'][0];
    $data['paymentId'] = (int) $meta['paymentId'][0];
    $data['createdAt'] = $post->post_date;
    return $data;
  }
}
