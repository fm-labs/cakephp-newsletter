<div class="form">
    <?= $this->Form->create(null); ?>
    <?= $this->Form->control('mailer_action'); ?>
    <?= $this->Form->submit(__d('newsletter', 'Send Email')); ?>
</div>