<?php

require_once('lib/BasePostType.php');

class Camp extends BasePostType {
  
  public static $type = "camp";

  public $attributes = array(
    'id', 'name', 'startTime', 'endTime', 'startDate', 'price', 'isOpen', 'isActive', 'maxPlayers', 'totalPlayers'
  );

  public function validate($model) {
    return true;
  }
  
  public function transformRequest($data) {
    $data = (object) $data;
    $className = get_class($this);
    $transformed = array();
    $transformed['post'] = array(
      'ID' => $data->id ? $data->id : null,
      'post_title' => $data->name,
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
    $data['id']           = (int) $post->ID;
    $data['name']         = $post->post_title;
    $data['startTime']    = $meta['startTime'][0];
    $data['endTime']      = $meta['endTime'][0];
    $data['startDate']    = $meta['startDate'][0];
    $data['price']        = $meta['price'][0];
    $data['isOpen']       = $meta['isOpen'][0];
    $data['isActive']     = $meta['isActive'][0];
    $data['maxPlayers']   = (int) $meta['maxPlayers'][0];
    $data['totalPlayers'] = (int) $meta['totalPlayers'][0];
    $data['createdAt']    = $post->post_date;
    return $data;
  }
}
