<? $message = $this->session->flashdata('message');?>

<? if(!empty($message)):?>
<div>
    <? echo $message;?>
</div>
<? endif;?>