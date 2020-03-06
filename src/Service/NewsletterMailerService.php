<?php

namespace Newsletter\Service;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Newsletter\Mailer\NewsletterMailer;
use Newsletter\Model\Table\NewsletterMembersTable;

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
            'Newsletter.Model.Member.afterSubscribe' => 'afterSubscribe',
            'Newsletter.Model.Member.afterUnsubscribe' => 'afterUnsubscribe'
        ];
    }

    /**
     * @param Event $event
     * @return void
     */
    public function afterSubscribe(Event $event)
    {
        $member = $event->getData('member');

        $logMsg = "NewsletterMailerService::afterSubscribe: " . $member->email . " - ";
        Log::info(sprintf(
            $logMsg . "%s|%s|%s",
            $member->greeting,
            $member->last_name,
            $member->first_name
        ), ['newsletter']);

        // Email to member
        try {
            if ($member->status == NewsletterMembersTable::STATUS_SUBSCRIBED) {
                (new NewsletterMailer())->send('memberSubscribe', [ $member ]);
            } elseif ($member->status == NewsletterMembersTable::STATUS_PENDING) {
                (new NewsletterMailer())->send('memberPending', [ $member ]);
            }
        } catch (\Exception $ex) {
            Log::error('NewsletterMailerService::afterSubscribe: ' . $ex->getMessage(), ['newsletter']);
        }
    }

    /**
     * @param Event $event
     * @return void
     */
    public function afterUnsubscribe(Event $event)
    {
        $member = $event->getData('member');

        $logMsg = "NewsletterMailerService::afterUnsubscribe: " . $member->email . " - ";
        Log::info(sprintf(
            $logMsg . "%s|%s|%s",
            $member->greeting,
            $member->last_name,
            $member->first_name
        ), ['newsletter']);

        // Email to member
        try {
            if ($member->status = NewsletterMembersTable::STATUS_UNSUBSCRIBED) {
                (new NewsletterMailer())->send('memberUnsubscribe', [$member]);
            }
        } catch (\Exception $ex) {
            Log::error('NewsletterMailerService::afterUnsubscribe: ' . $ex->getMessage(), ['newsletter']);
        }
    }
}
