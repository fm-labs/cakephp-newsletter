<?php
return [
    'Newsletter.Email' => [
        'memberPending' => [
            'subject' => 'Please confirm your newsletter subscription',
            'template' => 'Newsletter.member_pending',
        ],
        'memberSubscribe' => [
            'subject' => 'Your newsletter subscription was successful',
            'template' => 'Newsletter.member_subscribe',
        ],
        'memberUnsubscribe' => [
            'subject' => 'Unsubscribe confirmation',
            'template' => 'Newsletter.member_unsubscribe',
        ]
    ],
];
