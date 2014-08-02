#Basic Auth 
***

A basic authentication for codeigniter 2. That has three modes:

* Basic the password is not encrypted this would be good for those legacy code that are porting to codeigniter.

* Medium the password is base64 encoded, do note that bas64 is not encryption it just some old codes use this just to hide the real password so that it cannot be read in the database.

* Secured, this is a one way hash with a salt key similar to ion_auth and redux_auth

##How to setup
***
* Copy the folders config, controllers, libraries, models and views of Basic Auth package on your CI application folder.

* Create the a database and run the sql file on /sql folder

* Access the admin thru {your url}/admin , the details are :
<pre>
user: admin@admin.com
password: password
</pre>

* Put this code on the controller methods you would like to be password protected.  All methods of controller.

<pre>
	class Reports extends CI_Controller{
		
	public function __construct
	{
		parent::__construct();

		if(!$this->basic_auth->is_logged())
		{
		  redirect('/admin/auth/login');
		}
	}
</pre>






