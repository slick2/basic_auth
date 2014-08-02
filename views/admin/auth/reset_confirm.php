<?php $this->load->view('admin/common/header'); ?>

<?php if ($code_status == TRUE): ?>
    <!-- Change password form -->
    <form method="post" >
        <div>
            <?php echo validation_errors(); ?>
        </div>
        <div>
            <label>New Password</label>
            <input type="password" name="password" />
        </div>

        <div>
            <label>Confirm Password</label>
            <input type="password" name="passconf" />
        </div>

        <div>
            <input type="submit" value="Submit" />
        </div>
    </form>
<?php else: ?>
    <!-- Notification of wrong code -->
    <p>The reset code is invalid</p>
<?php endif; ?>

<?php $this->load->view('admin/common/footer'); ?>