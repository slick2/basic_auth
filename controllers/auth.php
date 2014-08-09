<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * @package CodeIgniter Basic Auth
 * @version 1.0
 * @author Carey Dayrit <code@webpagecoders.com>
 */
class Auth extends CI_Controller {

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

	public function index()
	{
		if ($this->basic_auth->is_logged())
		{
			// or redirect this to a landing like members or dashboard url
			$this->load->view('auth/index');
		}
		else
		{
			redirect('auth/login');
		}
		
	}

	public function register()
	{
		if ($this->basic_auth->is_logged())
		{
			redirect(base_url());
		}

		$data['identity'] = $this->config->item('identity');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		if ($data['identity'] == 'username')
		{
			$this->form_validation->set_rules('username', 'Username', 'required');
		}
		$this->form_validation->set_rules('last_name', 'Last name', 'required');
		$this->form_validation->set_rules('first_name', 'First name', 'required');

		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');


		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('auth/register', $data);
		}
		else
		{
			$info = array(
				'email' => $this->input->post('email'),
				'last_name' => $this->input->post('last_name'),
				'first_name' => $this->input->post('first_name'),
				'group_id' => $this->config->item('default_group'),
				'password' => $this->input->post('password'),
				'createdon' => date('Y-m-d H:i:s', time())
			);
			if ($this->config->item('identity') == 'username')
			{
				$info['username'] = $this->input->post('username');
			}

			$result = $this->basic_auth->register($info);

			if ($result)
			{
				if ($this->config->item('email_activation'))
				{
					$this->session->set_flashdata('message', 'An email has been sent with the activation code');
				}
				else
				{
					$this->session->set_flashdata('message', 'Registration success');
				}
			}
			else
			{
				$this->sesssion->set_flashdata('message', 'Register Failed, try again');
			}
			redirect(base_url() . 'auth/status');
		}
	}

	public function login()
	{
		$this->form_validation->set_rules('login', 'Login', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');


		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('auth/login');
		}
		else
		{
			$login = $this->input->post('login');
			$password = $this->input->post('password');
			$result = $this->basic_auth->login($login, $password);
			if ($result)
			{
				redirect(base_url());
			}
			else
			{
				$errors = implode(',', $this->basic_auth->errors());
				$this->session->set_flashdata('message', $errors);
				redirect(base_url() . 'auth/login');
			}
		}
	}

	public function logout()
	{
		$this->basic_auth->logout();
		redirect(base_url());
	}

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
			$mail_body = $this->load->view('auth/email/reset_password', $data, true);
			//TODO: use CI email lib\
			$headers = 'From: webmaster@example.com' . "\r\n" .
					'Reply-To: webmaster@example.com' . "\r\n" .
					'X-Mailer: PHP/' . phpversion();


			mail($email, 'Password Reset', $mail_body, $headers);
			//notify the user for reset
			$data['email_sent'] = TRUE;
		}

		$this->load->view('auth/reset_password', $data);
	}

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
		$this->load->view('auth/reset_confirm', $data);
	}

	public function status()
	{
		$this->load->view('auth/status');
	}

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
