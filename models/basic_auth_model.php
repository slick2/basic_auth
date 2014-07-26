<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * @package CodeIgniter Basic Auth
 * @version 1.0
 * @author Carey Dayrit <code@webpagecoders.com>
 */
class Basic_auth_model extends CI_Model {

	public $tables = array();
	public $identity_column;

	public function __construct()
	{
		parent::__construct();
		$this->load->config('basic_auth');
		$this->tables = $this->config->item('tables');
		$this->identity_column = $this->config->item('identity');
	}

	/**
	 * Method: Login
	 * @param string $identity
	 * @param string $password
	 * @return array
	 */
	public function login($identity, $password)
	{

		$query = $this->db->select($this->identity_column . ', password, activation_code ')
				->from($this->tables['users'])
				->where($this->identity_column, $identity)
				->where('password', $password)
				->get();

		if ($query->num_rows() == 1)
		{
			return $query->row_array();
		}
		else
		{
			return array();
		}
	}

	/**
	 * Method: Register
	 * @param array $data
	 */
	public function register($data)
	{
		$this->db->insert($this->tables['users'], $data);
	}

	/**
	 * Method: change_password
	 * @param string $identity
	 * @param string $new
	 * @return boolean
	 */
	public function change_password($identity = NULL, $new = NULL)
	{
		if (empty($new) OR empty($identity))
		{
			return FALSE;
		}
		$data = array
			(
			'password' => $new
		);
		return $this->db->where($this->identity_column, $identity)
						->update($this->tables['users'], $data);
	}

	/**
	 * Method: deactivate
	 * @param string $identity
	 * @return boolean
	 */
	public function deactivate($identity)
	{
		$users_table = $this->tables['users'];
		if ($identity === FALSE)
		{
			return FALSE;
		}

		$activation_code = sha1(md5(microtime()));
		$this->activation_code = $activation_code;

		$data = array('activation_code' => $activation_code, 'active' => 0);

		$this->db->update($users_table, $data, array($this->identity_column => $identity));

		return ($this->db->affected_rows() == 1) ? true : false;
	}

	/**
	 * Method: check_identity
	 * @param string $identity
	 * @return boolean
	 */
	public function check_identity($identity)
	{
		$query = $this->db->where($this->identity_column, $identity)
				->get($this->tables['users']);

		if ($query->num_rows())
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Method: exists_email
	 * @param string $email
	 * @return boolean
	 */
	public function exist_email($email)
	{
		$query = $this->db->get_where('users', array('email' => $email));
		if ($query->num_rows())
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Method: get_info
	 * @param string $email
	 * @return array
	 */
	public function get_info($email)
	{
		$result = array();
		$query = $this->db->get_where('users', array('email' => $email));
		$result = $query->result_array();
		return $result;
	}

}
