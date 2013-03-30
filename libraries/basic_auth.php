<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class basic_auth {
    private $errors = array();
    
    private $identiy_column;

    public function __construct() {
        $this -> load -> config('basic_auth');
        $this -> load -> model('basic_auth_model', 'Basic_Auth');
        $this->identity_column = $this->config->item('identity');

    }

    public function __get($var) {
        return   get_instance() -> $var;
    }

    public function activate($code) {
        //TODO : email activation
    }

    public function update_password($identity, $old, $new) {

    }

    public function deactivate($identity) {

    }

    public function forgotten($email) {

    }

    public function forgotten_password_complete($code) {

    }

    public function is_logged() {
        $identity = $this->config->item('identity');
        return ($this->session->userdata($identity)) ? true : false;

    }

    public function login($identity, $password) {
        switch($this->config->item('basic_auth_mode')) {
            case 1:
               $password = $this->hash_password($password);  
            break;
                
            case 2:
                $password = base64_encode($password);
            break;
                
            case 3:
            default:
                $password= $password;         
            break;         
          } 
        $profile = $this->Basic_Auth->login($identity, $password);
        
        if(!empty($profile)){
            $this->session->set_userdata($this->identity_column, $identity); 
            return true;           
        }else{
            $this->errors[]='The '.$this->identity_column.' and password does not match';           
        }     
        
        return false;
        
    }

    public function logout() {
        $identity = $this->config->item('identity');
        $this->session->unset_userdata($identity);
        $this->session->sess_destroy();
    }

    public function profile() {
        //TODO : profile
    }

    public function register($data) {
        //check the data first
        switch($this->config->item('basic_auth_mode')) {
            case 1 :
                //secure with salt
                $data['password'] = $this->hash_password($data['password']);
                break;
            case 2 :
                //semi not secured it is just encoded
                $data['password'] = base64_encode($data['password']);
                break;
            case 3 :
            //stupid, uncrypted password
            default
            ;
                $data['password'] = $data['password'];
                break;
        }

        $this -> Basic_Auth -> register($data);
        return true;
    }

    public function hash_password($password = false) {
        $salt_length = $this -> config -> item('salt_length');

        if ($password === false) {
            return false;
        }

        $salt = $this -> salt();

        $password = $salt . substr(sha1($salt . $password), 0, -$salt_length);

        return $password;
    }

    public function salt() {
        return substr(md5(uniqid(rand(), true)), 0, $this->config->item('salt_length'));
    }

    public function errors() {
        return $this->errors;
    }

}
