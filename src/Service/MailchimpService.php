<?php

namespace Newsletter\Service;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Mailchimp\Event\MailchimpWebhookEvent;

/**
 * Class MailchimpService
 *
 * @package Newsletter\Event
 */
class MailchimpService implements EventListenerInterface
{

    /**
     * @var \Newsletter\Model\Table\NewsletterMembersTable
     */
    public $NewsletterMembers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->NewsletterMembers = TableRegistry::get('Newsletter.NewsletterMembers');
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Newsletter.Model.Member.beforeSubscribe' => 'beforeSubscribe',
            'Newsletter.Model.Member.afterSubscribe' => 'afterSubscribe',

            'Mailchimp.Webhook.event' => 'mailchimpEvent',
            'Mailchimp.Webhook.subscribe' => 'mailchimpSubscribe',
            'Mailchimp.Webhook.unsubscribe' => 'mailchimpUnsubscribe',
            'Mailchimp.Webhook.profile' => 'mailchimpProfile',
            'Mailchimp.Webhook.upemail' => 'mailchimpChangeEmail',
            'Mailchimp.Webhook.cleaned' => 'mailchimpCleaned',
            'Mailchimp.Webhook.campaign' => 'mailchimpCampaign',
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
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpEvent(MailchimpWebhookEvent $event)
    {
        Log::info("Mailchimp: Event: " . $event->name(), ['newsletter']);
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpSubscribe(MailchimpWebhookEvent $event)
    {
        try {
            Log::info("Mailchimp: Subscriber: " . $event->getEmail(), ['newsletter']);
            $data = $this->_extractMemberData($event->data());
            // we can set optIn to FALSE here, because when we receive the "subscribe" event from mailchimp,
            // it is granted that the user already opted-in, if configured so in Mailchimp List settings
            if (!$this->NewsletterMembers->subscribe($event->getEmail(), $data, ['optIn' => false])) {
                throw new \RuntimeException("Subscribe failed");
            }
        } catch (\Exception $ex) {
            Log::info("Mailchimp: EventHandler failed: " . $event->name() . ":" . $ex->getMessage(), ['newsletter']);
        }
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpUnsubscribe(MailchimpWebhookEvent $event)
    {
        try {
            Log::info("Mailchimp: Unsubscribe: " . $event->getEmail(), ['newsletter']);
            if (!$this->NewsletterMembers->unsubscribe($event->getEmail())) {
                throw new \RuntimeException("Unsubscribe failed");
            }
        } catch (\Exception $ex) {
            Log::info("Mailchimp: EventHandler failed: " . $event->name() . ":" . $ex->getMessage(), ['newsletter']);
        }
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpProfile(MailchimpWebhookEvent $event)
    {
        try {
            Log::info("Mailchimp: ProfileUpdate: " . $event->getEmail(), ['newsletter']);
            $data = $this->_extractMemberData($event->data());
            if (!$this->NewsletterMembers->updateProfile($event->getEmail(), $data)) {
                throw new \RuntimeException("Update profile failed");
            }
        } catch (\Exception $ex) {
            Log::info("Mailchimp: EventHandler failed: " . $event->name() . ":" . $ex->getMessage(), ['newsletter']);
        }
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpChangeEmail(MailchimpWebhookEvent $event)
    {
        Log::info("Mailchimp: EmailChange: " . $event->getEmail(), ['newsletter']);
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpCleaned(MailchimpWebhookEvent $event)
    {
        Log::info("Mailchimp: Cleaned: " . $event->getEmail(), ['newsletter']);
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpCampaign(MailchimpWebhookEvent $event)
    {
    }

    /**
     * Extracts member info from webhook event data
     */
    protected function _extractMemberData($raw)
    {
        $map = [
            'email_type' => 'email_format',
            'merges.FNAME' => 'first_name',
            'merges.LNAME' => 'last_name',
        ];

        $data = [];
        foreach($map as $path =>$k) {
            if (Hash::check($raw, $path)) {
                $data[$k] = Hash::get($raw, $path);
            }
        }
        return $data;
    }
}
