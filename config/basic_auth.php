<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * Basic Auth Configuration
 * @package Basic Auth
 * @author Carey Dayrit <code@webpagecoders.com>
 * @version 1.0
 * 
 */
/**
 * mode 
 * 1 - feeling secured
 * 2 - semi secured
 * 3 - stupid (saves unencrypted password on the database)
 */
$config['basic_auth_mode'] = 1;

/**
 * A database column which is used to
 * login with.
 * */
$config['identity'] = 'email';

/**
 * Confirm code
 *
 * */
$config['confirm_salt'] = 'basic_auth'; //change this as needed

$config['salt'] = 'asdfasdfababasdgasdf';

/**
 * Email Activation for registration
 * */
$config['email_activation'] = true;

/**
 * Folder on which has the email template
 * 
 */

$config = 'auth/email';

/**
 * tables
 */
$config['tables'] = array('users' => 'users', 'groups' => 'groups');

/**
 * default group
 * 1 = administrator
 * 2 = member
 */
$config['default_group'] = 2;


/**
 * Salt Length
 * */
$config['salt_length'] = 10;

/**
 * Facebook Connect
 */
$config['facebook_app_id'] = '';
$config['facebook_secret'] = '';

/**
 * Twitter Connect
 */
$config['twitter_consumer_key'] = '';
$config['twitter_consumer_secret'] = '';
