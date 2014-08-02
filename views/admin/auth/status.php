<?php $this->load->view('admin/common/header');?>

<?php $message = $this->session->flashdata('message'); ?>

<?php if (!empty($message)): ?>
    <div>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php $this->load->view('admin/common/footer');?>