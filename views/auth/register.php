<form method="POST" action="<?php echo base_url() ?>auth/register">
    <?php echo validation_errors(); ?>
    <div>
        <div>
            <label>First Name</label>
            <input type="text" name="first_name" value="<? echo set_value('first_name')?>"/>        	        
        </div>

        <div>
            <label>Last Name</label>
            <input type="text" name="last_name" value="<? echo set_value('last_name')?>"/>
        </div>
        <div>
            <label><?php echo $identity == 'email' ? 'Email' : 'Username' ?></label>
            <input type="text" name="email" value="<? echo set_value('email')?>"/>
        </div>

        <?php if ($identity != 'email'): // don't require email if the identity is email ?>
            <div>
                <label>Email</label>
                <input type="text" name="username" value="<? echo set_value('username')?>" />
            </div>
        <?php endif; ?>
        <div>
            <label>Password</label>
            <input type="password" name="password" />
        </div>
        <div>
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" />
        </div>
        <div>
            <input type="submit" value="Login" />
        </div>
    </div>
</form>
