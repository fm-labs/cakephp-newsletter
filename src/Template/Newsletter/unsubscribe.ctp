<div class="newsletter form container">

    <h1><?= __d('newsletter', 'Unsubscribe Newsletter') ?></h1>

    <?= $this->Form->create($form); ?>
    <?= $this->Form->input('email', [
        'type' => 'email'
    ]); ?>
    <br />
    <?= $this->Form->submit(__d('newsletter', 'Submit')); ?>
    <?= $this->Form->end(); ?>

</div>