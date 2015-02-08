<?php

abstract class BasePostType {
    
    /**
     *  The post type
     */
    public static $type;

    /**
     * @param $request (object) - Model representation
     * @return (array) - Two keys, post and meta, each containing a wp object respectively
     */
    abstract public function transformRequest($request);

    /**
     * @param $post (object) - A wp post object
     * @param $meta (object) - A wp meta object
     * @return (object) - Model
     */    
    abstract public function transformResponse($post, $meta);

    /**
     *
     */    
    abstract public function validate($model);

    /**
     * Register the post type
     * @param $className (string) - The Model name, so we can access it's static property
     */
    public static function register($className) {
      $args = array(
        'public' => true,
        'label'  => $className::$type,
      );
      register_post_type($className::$type, $args);
    }

    /**
     * Is logged in wrapper
     */
    public function isLoggedIn() {
      return is_user_logged_in();
    }

    /**
     * Permission check
     * @param $action - string that the determines the capability
     * @return boolean
     */
    public function isAllowedTo($action) {
      $allowed = current_user_can('edit_posts');
      if(!$allowed) {
        $response = array('status' => '403', 'message'=>'You are not authorized to do this.');
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
      }
      return true;
    }

    /**
     * @param $query (object) - Has 2 keys limit, offset
     */
    public function findAll($query = array()) {
      $query = (object) $query;
      $className = get_class($this);
      $args = array();
      $limit = $query->limit ? $query->limit : 10;
      $offset = $query->offset ? $query->offset : 0;
      $args['posts_per_page'] = $limit;
      $args['offset'] = $offset;
      $args['post_status'] = 'publish';
      $args['post_type'] = $className::$type;
      $all = get_posts($args);
      $response = array();
      foreach($all as $model) {
        $meta = get_post_meta($model->ID);
        array_push($response, $this->transformResponse($model, $meta));
      }
      echo json_encode($response, JSON_PRETTY_PRINT);
      exit;
    }

    /**
     * 
     */
    public function findOne($id) {
      $post = get_post($id);
      $meta = get_post_meta($id);
      $response = $this->transformResponse($post, $meta);
      $response = $response['id'] !== 0 ? $response : null;
      echo json_encode($response, JSON_PRETTY_PRINT);
      exit;
    }

    /**
     * 
     */    
    public function create($data) {
      $data = (object) $data;
      $this->isAllowedTo('create');
      $this->validate($data);
      $transformed = $this->transformRequest($data);
      $id = wp_insert_post($transformed['post']);
      foreach($transformed['meta'] as $key => $value) {
        update_post_meta($id, $key, $value);
      }
      $data->id = $id; 
      echo json_encode($data, JSON_PRETTY_PRINT);
      exit;
    }
    
    /**
     * 
     */    
    public function update($data) {
      $data = (array) $data;
      $this->isAllowedTo('edit');
      $dbModel = get_post($data['id']);
      if(!$dbModel) {
        $response = array();
        $response['status'] = 404;
        $response['error'] = "Please provide a valid ID";
        echo json_encode($response);
        exit;
      }
      $modelMeta = get_post_meta($dbModel->ID);
      $transformedModel = $this->transformResponse($dbModel, $modelMeta);
      foreach($data as $key=>$value) {
        $transformedModel[$key] = $value;
      }
      $data = $transformedModel;
      $this->validate($data);
      $transformed = $this->transformRequest($data);
      $id = wp_update_post($transformed['post']);
      foreach($transformed['meta'] as $key => $value) {
        update_post_meta($id, $key, $value);
      }
      echo json_encode($data, JSON_PRETTY_PRINT);
      exit;
    }
    
    /**
     * 
     */    
    public function destroy($id) {
      $this->isAllowedTo('delete');
      $post = wp_delete_post($id, true);
      foreach($transformed['meta'] as $key => $value) {
        delete_post_meta($id, $key);
      }
      $response = $this->transformResponse($post, array());
      echo json_encode($response, JSON_PRETTY_PRINT);
      exit;
    }
}