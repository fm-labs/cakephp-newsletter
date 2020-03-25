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
        $this->NewsletterMembers = TableRegistry::getTableLocator()->get('Newsletter.NewsletterMembers');
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Newsletter.Model.Member.afterSubscribe' => 'afterSubscribe',
            'Newsletter.Model.Member.afterUnsubscribe' => 'afterUnsubscribe',

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
    public function afterSubscribe(Event $event)
    {
        if (isset($event->getData('options')['source']) && $event->getData('options')['source'] == 'mailchimp') {
            return;
        }

        try {
            Log::info("MailchimpService: LocalEvent: " . $event->getName(), ['newsletter']);
            $this->NewsletterMembers->mailchimp()
                ->subscribeMember($event->getData('member')['email']);
        } catch (\Exception $ex) {
            Log::info("MailchimpService: EventHandler failed: " . $event->getName() . ":" . $ex->getMessage(), ['newsletter']);
        }
    }

    /**
     * @param Event $event
     * @return void
     */
    public function afterUnsubscribe(Event $event)
    {
        if (isset($event->getData('options')['source']) && $event->getData('options')['source'] == 'mailchimp') {
            return;
        }

        try {
            Log::info("MailchimpService: LocalEvent: " . $event->getName(), ['newsletter']);
            $this->NewsletterMembers->mailchimp()
                ->unsubscribeMember($event->getData('member')['email']);
        } catch (\Exception $ex) {
            Log::info("MailchimpService: EventHandler failed: " . $event->getName() . ":" . $ex->getMessage(), ['newsletter']);
        }
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpEvent(MailchimpWebhookEvent $event)
    {
        Log::info("MailchimpService: Event: " . $event->getName(), ['newsletter']);
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpSubscribe(MailchimpWebhookEvent $event)
    {
        try {
            Log::info("MailchimpService: Subscribe: " . $event->getListId() . ":" . $event->getEmail(), ['newsletter']);

            $data = $this->_extractMemberData($event->getData());

            // we can set optIn to FALSE here, because when we receive the "subscribe" event from mailchimp,
            // it is granted that the user already opted-in, if configured so in Mailchimp List settings
            $options = [ 'optIn' => false, 'events' => true, 'source' => 'mailchimp' ];
            if (!$this->NewsletterMembers->subscribeMember($event->getEmail(), $data, $options)) {
                throw new \RuntimeException("Subscribe failed");
            }
        } catch (\Exception $ex) {
            Log::info("MailchimpService: EventHandler failed: " . $event->getName() . ":" . $ex->getMessage(), ['newsletter']);
        }
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpUnsubscribe(MailchimpWebhookEvent $event)
    {
        try {
            Log::info("MailchimpService: Unsubscribe: " . $event->getListId() . ":" . $event->getEmail(), ['newsletter']);

            $options = [ 'events' => true, 'source' => 'mailchimp' ];
            if (!$this->NewsletterMembers->unsubscribeMember($event->getEmail(), $options)) {
                throw new \RuntimeException("Unsubscribe failed");
            }
        } catch (\Exception $ex) {
            Log::info("MailchimpService: EventHandler failed: " . $event->getName() . ":" . $ex->getMessage(), ['newsletter']);
        }
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpProfile(MailchimpWebhookEvent $event)
    {
        try {
            Log::info("MailchimpService: ProfileUpdate: " . $event->getListId() . ":" . $event->getEmail(), ['newsletter']);

            $data = $this->_extractMemberData($event->getData());

            $options = [ 'events' => true, 'source' => 'mailchimp' ];
            if (!$this->NewsletterMembers->updateMember($event->getEmail(), $data, $options)) {
                throw new \RuntimeException("Update profile failed");
            }
        } catch (\Exception $ex) {
            Log::info("MailchimpService: EventHandler failed: " . $event->getName() . ":" . $ex->getMessage(), ['newsletter']);
        }
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpChangeEmail(MailchimpWebhookEvent $event)
    {
        Log::warning("MailchimpService: [NOTIMPLEMENTED] EmailChange: " . $event->getListId() . ":" . $event->getEmail(), ['newsletter']);
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpCleaned(MailchimpWebhookEvent $event)
    {
        Log::warning("MailchimpService: [NOTIMPLEMENTED]  Cleaned: " . $event->getListId() . ":" . $event->getEmail(), ['newsletter']);
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpCampaign(MailchimpWebhookEvent $event)
    {
        Log::warning("MailchimpService: [NOTIMPLEMENTED]  Campain: " . $event->getListId(), ['newsletter']);
    }

    /**
     * Extracts member info from webhook event data
     *
     * @param $raw
     * @return array
     */
    protected function _extractMemberData($raw)
    {
        $map = [
            'email_type' => 'email_format',
            'merges.FNAME' => 'first_name',
            'merges.LNAME' => 'last_name',
            'merges.ADDRESS' => 'address',
            'merges.PHONE' => 'phone',
        ];

        $data = [];
        foreach ($map as $path => $k) {
            if (Hash::check($raw, $path)) {
                $val = Hash::get($raw, $path);
                //if ($val) {
                    $data[$k] = $val;
                //}
            }
        }

        return $data;
    }
}
