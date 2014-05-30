<?php $message = $this->session->flashdata('message'); ?>

<?php if (!empty($message)): ?>
    <div>
        <?php echo $message; ?>
    </div>
<?php endif; ?>