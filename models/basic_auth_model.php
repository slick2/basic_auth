<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class basic_auth_model extends CI_Model
{
 
    public $tables = array();
    
    public $identity_column;
    
    public function __construct(){
        parent::__construct();
        $this->load->config('basic_auth');
        $this->tables  = $this->config->item('tables');
        $this->identity_column = $this->config->item('identity');
    }
    
    /**
     * Login
     */
    public function login($identity, $password){
            
        $query = $this->db->select($this->identity_column.', password, activation_code ')
            ->from($this->tables['users'])
            ->where($this->identity_column, $identity)
            ->where('password', $password)
            ->get();
         
         if($query->num_rows() ==1){
             return $query->row_array();             
         }else{
             return array();
         }      
    }
     
    /**
     * Register
     */
    
    public function register($data){
        $this->db->insert($this->tables['users'], $data);                 
    }
    
    /**
     * Change password
     */
    
    public function change_password($identity, $old, $new){
        
        
    }
     
    /**
     * logout
     */
    
    public function logout(){
        
        
    }
    
    public function check_identity($identity){
        
        
    }
    
    
     
    
}