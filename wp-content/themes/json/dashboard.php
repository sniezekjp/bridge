<?php // Template Name: Dashboard ?>
<?php
  error_reporting(-1);
  
  require_once("../../../wp-blog-header.php");

  require_once('api/Player.php');
  require_once('api/Camp.php');
  require_once('api/Contact.php');
  require_once('api/Payment.php');

  header('Content-Type', 'application/json');

  $json   = file_get_contents('php://input');
  $isPost = $json !== "";
  $_POST  = json_decode($json);

  $split = explode('/', $_GET['command']);

  $args = array();
  $args['method'] = $_SERVER['REQUEST_METHOD'];
  $args['model']  = $split[0];
  $args['id']     = $split[1];

  function factory($type) {
    switch($type) {
      case "player":
        return new Player();
        break;
      case "camp":
        return new Camp();
        break;
      case "contact":
        return new Contact();
        break;
      case "payment":
        return new Payment();
        break;
      default: 
        $response = array();
        $response['status'] = 404;
        $response['message'] = $type . " not found.";
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }
  }

  $model = factory($args['model']);

  // REST Router
  switch($args['method']) {
    case 'POST':
      if($args['id'] === 'notify') {
        $model->notify();
      } else {
        $model->create($_POST);
      }
      break;
    case 'PUT':
      if(!$_POST->id || empty($_POST->id)) {
        $_POST->id = $args['id'];
      }
      $model->update($_POST);
      break;
    case "DELETE":
      $model->destroy($args['id']);
      break;
    case "GET":
      if($args['id']){
        $model->findOne($args['id']);
      } else {
        $model->findAll($_GET);
      }
      break;
  }