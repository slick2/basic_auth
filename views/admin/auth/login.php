<?php $this->load->view('admin/common/header'); ?>

<?php $this->load->view('auth/status'); ?>

<h4>Login</h4>

<form method="post">
    <div>
        <label>Login</label>
        <input type="text" name="login" />
    </div>

    <div>
        <label>Password</label>
        <input type="password" name="password" />
    </div>

    <div>
        <a href="<?php echo base_url() ?>admin/auth/reset_password">Forgot Password</a>
        <br />
        <input type="submit" value="login" />
    </div>

</form>

<?php $this->load->view('admin/common/footer'); ?>

