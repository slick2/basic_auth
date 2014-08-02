<?php $this->load->view('admin/common/header'); ?>

<?php echo validation_errors(); ?>
<?php if ($email_sent): ?>
	<p>An email has been sent, for a request to reset your password</p>
<?php endif; ?>
<form method="post">
    Enter email <input type="text" name="email" />
    <input type="submit" value="submit" />
</form>

<?php $this->load->view('admin/common/header'); ?>