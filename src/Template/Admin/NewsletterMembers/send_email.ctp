<div class="form">
    <?= $this->Form->create(null); ?>
    <?= $this->Form->input('mailer_action'); ?>
    <?= $this->Form->submit(__('Send Email')); ?>
</div>