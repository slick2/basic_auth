<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class basic_auth
{

    private $errors = array();
    private $identiy_column;
    private $email_templates;

    public function __construct()
    {
        $this->load->config('basic_auth');
        $this->load->model('basic_auth_model', 'Basic_Auth');
        $this->load->library('email');

        $this->identity_column = $this->config->item('identity');

        //prep the email
        $this->email_templates = $this->config->item('email_templates');
    }

    public function __get($var)
    {
        return get_instance()->$var;
    }

    public function activate($code)
    {
        //TODO : email activation
    }

    public function change_password($email, $new)//change the password by email
    {
        //TODO : change password by identity username

        return $this->Basic_Auth->change_password($identity, $new);
    }

    public function deactivate($identity)
    {
        //TODO: deactivate account
    }

    public function forgotten_password($email)
    {
        //TODO: forgotten password
    }

    public function forgotten_password_complete($code)
    {
        //TODO: forgotten password complete
    }

    public function is_logged()
    {
        $identity = $this->config->item('identity');
        return ($this->session->userdata($identity)) ? TRUE : FALSE;
    }

    public function get_info($email)
    {
        return $this->Basic_Auth->get_info($email);
    }

    public function login($identity, $password)
    {
        switch ($this->config->item('basic_auth_mode')) {
            case 1 :
                $password = $this->hash_password($password);
                break;

            case 2 :
                $password = base64_encode($password);
                break;

            case 3 :
            default :
                $password = $password;
                break;
        }
        $profile = $this->Basic_Auth->login($identity, $password);

        if (!empty($profile)) {
            $this->session->set_userdata($this->identity_column, $identity);
            return true;
        } else {
            $this->errors[] = 'The ' . $this->identity_column . ' and password does not match';
        }

        return false;
    }

    public function logout()
    {
        $identity = $this->config->item('identity');
        $this->session->unset_userdata($identity);
        $this->session->sess_destroy();
    }

    public function profile()
    {
        //TODO : profile
    }

    public function exist_email($email)
    {
        return $this->Basic_Auth->exist_email($email);
    }

    public function register($data)
    {
        //check the data first
        switch ($this->config->item('basic_auth_mode')) {
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

        $this->Basic_Auth->register($data);

        if ($this->config->item('email_activation')) {
            $this->Basic_Auth->deactivate($data[$this->identity_column]);
            $email_activation = array(
                'email' => $data['email'],
                'activation' => $this->Basic_Auth->activation_code
            );
            $this->email_activation($data['email'], $email_activation);
        }
        return TRUE;
    }

    public function hash_password($password = FALSE)
    {
        $salt_length = $this->config->item('salt_length');

        if ($password === FALSE) {
            return FALSE;
        }
        $salt = $this->salt();

        $password = $salt . substr(sha1($salt . $password), 0, -$salt_length);

        return $password;
    }

    public function salt()
    {
        return substr(md5(uniqid(rand(), true)), 0, $this->config->item('salt_length'));
    }

    public function errors()
    {
        return $this->errors;
    }

    public function email_activation($email, $data = array())
    {

        $message = $this->load->view($this->email_templates . 'activation', $data);
        $this->email->clear();
        $this->email->set_newline("\r\n");
        $this->email->from('', '');
        $this->email->to($email);
        $this->email->subject('Email Activation (Registration)');
        $this->email->message($message);

        return $this->email->send();
    }

}
