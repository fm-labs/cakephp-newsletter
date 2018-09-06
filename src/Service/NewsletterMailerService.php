<?php

namespace Newsletter\Service;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Newsletter\Mailer\NewsletterMailer;
use Newsletter\Mailer\NewsletterOwnerMailer;

/**
 * Class NewsletterMailerService
 *
 * @package Newsletter\Event
 */
class NewsletterMailerService implements EventListenerInterface
{

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Newsletter.Model.Member.beforeSubscribe' => 'beforeSubscribe',
            'Newsletter.Model.Member.afterSubscribe' => 'afterSubscribe'
        ];
    }

    /**
     * @param Event $event
     * @return void
     */
    public function beforeSubscribe(Event $event)
    {
    }

    /**
     * @param Event $event
     * @return void
     */
    public function afterSubscribe(Event $event)
    {
        $subscriber = $event->data['member'];

        $logMsg = "[newsletter] SIGNUP:" . $subscriber->email . " - ";
        Log::info(sprintf($logMsg . "%s|%s|%s",
            $subscriber->greeting, $subscriber->last_name, $subscriber->first_name), ['newsletter']);

        // Email to subscriber
        try {
            (new NewsletterMailer())->send('subscriptionConfirmation',[$subscriber]);
        } catch (\Exception $ex) {
            Log::error('NewsletterMailerService::afterSubscribe: ' . $ex->getMessage(), ['newsletter']);
        }

        // Email to owner
        try {
            (new NewsletterOwnerMailer())->send('subscriptionNotify',[$subscriber]);
        } catch (\Exception $ex) {
            Log::error('NewsletterMailerService::afterSubscribe: ' . $ex->getMessage(), ['newsletter']);
        }
    }
}
