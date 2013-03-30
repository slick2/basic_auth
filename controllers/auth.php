<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
        //this should be in autoload
        $this->load->helper('url');
        $this->load->config('basic_auth');
        
        
		$this->load->library('form_validation');
        $this->load->library('session');
        $this->load->library('basic_auth');
	}
    
    public function index(){
      $this->load->view('auth/index');          
    }
    
    public function register(){
        if($this->basic_auth->is_logged()){
            redirect(base_url());            
        }
               
        $data['identity'] = $this->config->item('identity');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        if($data['identity']=='username'){
            $this->form_validation->set_rules('username', 'Username', 'required');           
        }
        $this->form_validation->set_rules('last_name', 'Last name', 'required');
        $this->form_validation->set_rules('first_name', 'First name', 'required');
        
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
        
        
        if($this->form_validation->run() == FALSE){          
            $this->load->view('auth/register', $data);    
        }else{
            $info=array(
                'email'=>$this->input->post('email'),
                'last_name' => $this->input->post('last_name'),
                'first_name' => $this->input->post('first_name'),
                'group_id' =>$this->config->item('default_group'),
                'password' =>$this->input->post('password'),
                'createdon' => date('Y-m-d H:i:s', time())
                             
            );
            if($this->config->item('identity')=='username'){
                $info['username'] = $this->input->post('username');               
            }
            
            $result = $this->basic_auth->register($info);
            
            if($result){
               if($this->config->item('email_activation')){
                   $this->session->set_flashdata('message', 'Email');
               }else{
                   $this->session->set_flashdata('message', 'Registration success');                                     
               }                             
           
            }else{
                $this->sesssion->set_flashdata('message', 'Register Failed, try again');               
            }         
            redirect(base_url().'auth/status');
            
        }
    }

    public function login(){
        $this->form_validation->set_rules('login', 'Login', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');
        
        if($this->form_validation->run()==FALSE){
            $this->load->view('auth/login'); 
        }else{
            $login = $this->input->post('login');
            $password = $this->input->post('password');
            $result = $this->basic_auth->login($login, $password);
            if($result){
                redirect(base_url());                
            }else{
                $errors = implode(',', $this->basic_auth->errors());
                $this->session->set_flashdata('message',$errors);               
                redirect(base_url().'auth/login');
            }
        } 
    }
    
    public function logout(){
        $this->basic_auth->logout();
        redirect(base_url());
        
    }

    public function status(){
        $this->load->view('auth/status');       
    }
	
}
