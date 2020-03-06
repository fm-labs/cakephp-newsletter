<div class="newsletter form container">

    <h1><?= __d('newsletter', 'Newsletter Signup Form') ?></h1>

    <?= $this->Form->create($form); ?>
    <?= $this->Form->control('greeting', [
        'type' => 'select',
        'options' => ['mr' => __d('newsletter', 'Mr.'), 'ms' => __d('newsletter', 'Ms.'), 'comp' => __d('newsletter', 'Company')],
        'label' => __d('newsletter', 'Greeting')
    ]) ?>
    <?= $this->Form->control('title', [
        'label' => __d('newsletter', 'Title')
    ]); ?>

    <?= $this->Form->control('first_name', [
        'label' => __d('newsletter', 'First name')
    ]); ?>

    <?= $this->Form->control('name', [
        'label' => __d('newsletter', 'Last name')
    ]); ?>
    <?= $this->Form->control('email', [
        'type' => 'email'
    ]); ?>
    <br />
    <?= $this->Form->submit(__d('newsletter', 'Submit')); ?>
    <?= $this->Form->end(); ?>

</div>