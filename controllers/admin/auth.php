<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * @package Basic Auth
 * @version 1.0
 * @author Carey Dayrit <code@webpagecoders.com>
 */
class Auth extends CI_Controller {

	/**
	 * Description: put here the landing page after login, make sure the views
	 * is named after this setup.
	 * 
	 * Example:
	 * Redirect to /admin/dashboard 
	 * there should be a controller /admin/dashboard.php
	 * there should be a view /views/admin/dashboard.php 
	 * 
	 * OR
	 * Redirect to /admin/dashboard/index
	 * there should be a controller /admin/dashboard.php and a method index
	 * there should be a view file in /views/admin/dashboard/index.php
	 * 
	 * @var string  
	 */
	public $landing = 'admin/auth/index';

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		//this should be in autoload
		$this->load->helper('url');
		$this->load->config('basic_auth');


		$this->load->library('form_validation');
		$this->load->library('session');
		$this->load->library('basic_auth');
	}

	/**
	 * Method: index
	 * Description: Index or Default page
	 */
	public function index()
	{
		//check 
		if ($this->basic_auth->is_logged())
		{
			$this->load->view($this->landing);
		}
		else
		{
			redirect('admin/auth/login');
		}
	}

	/**
	 * Login
	 */
	public function login()
	{
		$this->form_validation->set_rules('login', 'Login', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');

		$this->basic_auth->change_password('admin@admin.com', 'password');

		if ($this->form_validation->run() === FALSE)
		{
			$this->load->view('admin/auth/login');
		}
		else
		{
			$login = $this->input->post('login');
			$password = $this->input->post('password');
			$result = $this->basic_auth->login($login, $password);

			if ($result)
			{
				redirect($this->landing);
			}
			else
			{
				$errors = implode(',', $this->basic_auth->errors());
				$this->session->set_flashdata('message', $errors);
				redirect('/admin/auth/login');
			}
		}
	}

	/**
	 * Method: Logout
	 */
	public function logout()
	{
		$this->basic_auth->logout();
		redirect('/admin/auth/login');
	}

	/**
	 * Method: reset_password
	 * Description: resets the password
	 */
	public function reset_password()
	{
		$data['email_sent'] = FALSE;
		$this->form_validation->set_rules('email', 'Email', 'required|callback_exist_email');
		if ($this->form_validation->run() == TRUE)
		{
			//reset the password
			//we should email first before resetting
			//produce an hash, email + encryption key
			$email = $this->input->post('email');
			//salted code
			$salted_code = $this->config->item('confirm_salt');
			$user_info = $this->basic_auth->get_info($email);

			$reset_code = urlencode($email . '|') . md5($email . $salted_code);
			$data['user_info'] = $user_info;
			$data['reset_code'] = $reset_code;
			$mail_body = $this->load->view('admin/auth/email/reset_password', $data, true);
			//TODO: use CI email lib\
			$headers = 'From: webmaster@example.com' . "\r\n" .
					'Reply-To: webmaster@example.com' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();


			mail($email, 'Password Reset', $mail_body, $headers);
			//notify the user for reset
			$data['email_sent'] = TRUE;
		}

		$this->load->view('admin/auth/reset_password', $data);
	}

	/**
	 * 
	 * @param string $code 
	 */
	public function reset_confirm($code = null)
	{
		$code_status = FALSE;



		if (empty($code))
		{
			//set the flash
			$code_status = FALSE;
		}
		//extract code
		$reset_code = explode('|', urldecode($code));
		$email = $reset_code[0];
		$salted_code = $reset_code[1];

		//check if the email exist
		if (!$this->exist_email($email))
		{
			$code_status = FALSE;
		}
		//check the reset code
		$reference_code = md5($email . $this->config->item('confirm_salt'));
		if ($salted_code != $reference_code)
		{
			$code_status = FALSE;
		}
		else
		{
			//put the new password
			$code_status = TRUE;
			$this->form_validation->set_rules('password', 'Password', 'required|matches[passconf]');
			$this->form_validation->set_rules('passconf', 'Confirm Password', 'required');

			if ($this->form_validation->run() == TRUE)
			{
				//reset the password
				$this->basic_auth->change_password($email, $this->input->post('password'));
				//flash message
				$this->session->set_flashdata('password_reset', 'Success password has been reset');
				//redirect to login
				redirect('/admin/auth/login');
			}
		}
		$data['code_status'] = $code_status;
		$this->load->view('admin/auth/reset_confirm', $data);
	}

	/**
	 * Method: status
	 */
	public function status()
	{
		$this->load->view('admin/auth/status');
	}

	/**
	 * Method: exist_email
	 * @param string $email
	 * @return boolean
	 */
	public function exist_email($email)
	{
		if (!$this->basic_auth->exist_email($email))
		{
			$this->form_validation->set_message('exist_email', 'The email is not registered');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

}
