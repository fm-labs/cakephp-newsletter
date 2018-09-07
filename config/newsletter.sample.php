<?php
/**
 * Default Newsletter config
 *
 * Copy to local app config folder and uncomment lines to override
 */
return [
    'Newsletter' => [
        'layout' => null, // the layout used in the NewsletterController
        'Mailer' => [
            'enabled' => false,
            'profile' => 'default'
        ],

        'Mailchimp' => [
            'enabled' => false,
            'api_key' => '', // Mailchimp API Key
            'list_id' => '', // Mailchimp List ID
        ]
    ]
];
