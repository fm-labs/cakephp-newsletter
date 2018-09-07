<div class="panel panel-default">
    <div class="panel-heading">Latest Newsletter Signups</div>
    <?= $this->cell('Backend.DataTable', [[
        'paginate' => false,
        'model' => 'Newsletter.NewsletterMembers',
        'data' => $NewsletterMembers,
        'fields' => [
            'created',
            'display_name' => [
                'formatter' => function($val, $row) {
                    return $this->Html->link($val, ['plugin' => 'Newsletter', 'controller' => 'NewsletterMembers', 'action' => 'view', $row->id], ['class' => 'link-frame']);
                }
            ],
            //'email'
        ],
        'rowActions' => false
    ]]);
    ?>
    <div class="panel-footer">
        <?= $this->Html->link(__d('newsletter', 'View all newsletter signups'),
            ['plugin' => 'Newsletter', 'controller' => 'NewsletterMembers', 'action' => 'index'],
            ['class' => 'btn btn-default link-frame']); ?>
    </div>
</div>