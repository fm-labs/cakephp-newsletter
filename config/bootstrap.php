<?php

use Cake\Log\Log;
use Cake\Mailer\Email;

// Newsletter log config
if (!Log::getConfig('newsletter')) {
    Log::setConfig('newsletter', [
        'className' => 'Cake\Log\Engine\FileLog',
        'path' => LOGS,
        'file' => 'newsletter',
        //'levels' => ['notice', 'info', 'debug'],
        'scopes' => ['newsletter'],
    ]);
}

// Newsletter Email config
//if (!Email::getConfig('newsletter_owner_notify')) {
//    Email::setConfig('newsletter_owner_notify', [
//        'transport' => 'default',
//        //'from' => 'notify@localhost',
//        //'to' => '', // <-- INSERT OWNER EMAIL HERE
//        'subject' => 'Newsletter Notification'
//    ]);
//}
//
//if (!Email::getConfig('newsletter_user_notify')) {
//    Email::setConfig('newsletter_user_notify', [
//        'transport' => 'default',
//        //'from' => 'notify@localhost',
//        'subject' => 'Newsletter Notification'
//    ]);
//}
