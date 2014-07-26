#Basic Auth 
==========

A basic authentication for codeigniter 2. It has three modes:

* Basic the password is not encrypted this would be good for those legacy code that are porting to codeigniter.

* Medium the password is base64 encoded, do note that bas64 is not encryption it just some old codes use this just to hide the real password so that it cannot be read in the database.

* Secured, this is a one way hash with a salt key similar to ion_auth and redux_auth