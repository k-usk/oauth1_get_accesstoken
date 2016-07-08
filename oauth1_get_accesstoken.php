<?php
/*
Plugin Name: OAuth1.0a get AccessToken Plugin
Plugin URI:
Description: WP REST APIのOAuth1.0a認証用のアクセストークンを取得する
Version:     0.1
Author:      Yusuke Kano
Author URI:
License: GPL2+
*/

class oauth1_get_accesstoken{

  public $isErr;
  public $err;
  public $access_token;

  public function __construct(){
    add_action( 'admin_menu', [$this,'add_plugin_admin_menu'] );
  }

  public function add_plugin_admin_menu() {
    add_users_page(
        'Get AccessToken',
        'Get AccessToken',
        'administrator',
        'get-accesstoken',
        [$this,'display_plugin_admin_page']
    );
  }

  public function display_plugin_admin_page() {
    include_once( 'inc/view_options.php' );
  }

  public function get_verification_token(){
    require_once(dirname(__FILE__).'/inc/GetOAuthToken.php');
    $oauth = new GetOAuthToken();
    if(array_key_exists('client_key', $_POST) && array_key_exists('client_secret', $_POST)){
      $client_key = $_POST['client_key'];
      $client_secret = $_POST['client_secret'];
      if(strlen($client_key) == 12 && strlen($client_secret) == 48){
        try{
          $oauth->goToAuthorize($client_key, $client_secret);
        } catch (\Exception $e) {
          $this->isErr = true;
          $this->err = $e->getMessage();
        }
      }else{
        //エラー
        $this->isErr = true;
        $this->err = 'Client KeyとClient Secretを正しく入力して下さい';
      }
    }else{
      //エラー
      $this->isErr = true;
      $this->err = 'Client KeyとClient Secretを入力して下さい';
    }
  }

  public function get_access_token(){
    require_once(dirname(__FILE__).'/inc/GetOAuthToken.php');
    $oauth = new GetOAuthToken();
    if(array_key_exists('oauth_verifier', $_GET)){
      if(strlen($_GET['oauth_verifier']) == 24){
        try{
          $this->access_token = $oauth->getAccessToken();
        }catch (\Exception $e) {
          $this->isErr = true;
          $this->err = $e->getMessage();
        }
      }else{
        //エラー
        $this->isErr = true;
        $this->err = 'Verification tokenを正しく入力して下さい';
      }
    }else{
      //エラー
      $this->isErr = true;
      $this->err = 'Verification tokenを入力して下さい';
    }
  }
}

if( is_admin() ) {
    $oauth1_get_accesstoken = new oauth1_get_accesstoken();

    if(array_key_exists('action', $_GET)){
      if($_GET['action'] == 'verify'){
        $oauth1_get_accesstoken->get_verification_token();
      }else if($_GET['action'] == 'done'){
        $oauth1_get_accesstoken->get_access_token();
      }
    }
}
