<div class="newsletter form container">

    <h1><?= __d('newsletter', 'Newsletter Signup Form') ?></h1>

    <?= $this->Form->create($form); ?>
    <?= $this->Form->input('greeting', [
        'type' => 'select',
        'options' => ['mr' => __d('newsletter', 'Mr.'), 'ms' => __d('newsletter', 'Ms.'), 'comp' => __d('newsletter', 'Company')],
        'label' => __d('newsletter', 'Greeting')
    ]) ?>
    <?= $this->Form->input('title', [
        'label' => __d('newsletter', 'Title')
    ]); ?>

    <?= $this->Form->input('first_name', [
        'label' => __d('newsletter', 'First name')
    ]); ?>

    <?= $this->Form->input('name', [
        'label' => __d('newsletter', 'Last name')
    ]); ?>
    <?= $this->Form->input('email', [
        'type' => 'email'
    ]); ?>
    <br />
    <?= $this->Form->submit(__d('newsletter', 'Submit'), ['class' => 'ui primary button']); ?>
    <?= $this->Form->end(); ?>

</div>