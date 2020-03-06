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
 *
 * @deprecated Use MailchimpService instead
 */
class MailchimpListService implements EventListenerInterface
{

    /**
     * @var \Newsletter\Model\Table\NewsletterListsTable
     */
    public $NewsletterLists;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->NewsletterLists = TableRegistry::getTableLocator()->get('Newsletter.NewsletterLists');
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Newsletter.List.Member.beforeSubscribe' => 'beforeSubscribe',
            'Newsletter.List.Member.afterSubscribe' => 'afterSubscribe',

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
        Log::info("Mailchimp: Event: " . $event->getName(), ['newsletter']);
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpSubscribe(MailchimpWebhookEvent $event)
    {
        try {
            Log::info("Mailchimp: Subscriber: " . $event->getListId() . ":" . $event->getEmail(), ['newsletter']);

            $list = $this->_findMailchimpList($event->getListId());
            $data = $this->_extractMemberData($event->data());

            // we can set optIn to FALSE here, because when we receive the "subscribe" event from mailchimp,
            // it is granted that the user already opted-in, if configured so in Mailchimp List settings
            $options = [ 'optIn' => false, 'events' => true, 'source' => 'mailchimp' ];
            if (!$this->NewsletterLists->subscribeMember($list, $event->getEmail(), $data, $options)) {
                throw new \RuntimeException("Subscribe failed");
            }
        } catch (\Exception $ex) {
            Log::info("Mailchimp: EventHandler failed: " . $event->getName() . ":" . $ex->getMessage(), ['newsletter']);
        }
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpUnsubscribe(MailchimpWebhookEvent $event)
    {
        try {
            Log::info("Mailchimp: Unsubscribe: " . $event->getListId() . ":" . $event->getEmail(), ['newsletter']);

            $list = $this->_findMailchimpList($event->getListId());

            $options = [ 'events' => true, 'source' => 'mailchimp' ];
            if (!$this->NewsletterLists->unsubscribeMember($list, $event->getEmail(), $options)) {
                throw new \RuntimeException("Unsubscribe failed");
            }
        } catch (\Exception $ex) {
            Log::info("Mailchimp: EventHandler failed: " . $event->getName() . ":" . $ex->getMessage(), ['newsletter']);
        }
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpProfile(MailchimpWebhookEvent $event)
    {
        try {
            Log::info("Mailchimp: ProfileUpdate: " . $event->getListId() . ":" . $event->getEmail(), ['newsletter']);

            $list = $this->_findMailchimpList($event->getListId());
            $data = $this->_extractMemberData($event->data());

            $options = [ 'events' => true, 'source' => 'mailchimp' ];
            if (!$this->NewsletterLists->updateMember($list, $event->getEmail(), $data, $options)) {
                throw new \RuntimeException("Update profile failed");
            }
        } catch (\Exception $ex) {
            Log::info("Mailchimp: EventHandler failed: " . $event->getName() . ":" . $ex->getMessage(), ['newsletter']);
        }
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpChangeEmail(MailchimpWebhookEvent $event)
    {
        Log::warning("Mailchimp: [NOTIMPLEMENTED] EmailChange: " . $event->getListId() . ":" . $event->getEmail(), ['newsletter']);
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpCleaned(MailchimpWebhookEvent $event)
    {
        Log::warning("Mailchimp: [NOTIMPLEMENTED]  Cleaned: " . $event->getListId() . ":" . $event->getEmail(), ['newsletter']);
    }

    /**
     * @param Event|MailchimpWebhookEvent $event
     */
    public function mailchimpCampaign(MailchimpWebhookEvent $event)
    {
        Log::warning("Mailchimp: [NOTIMPLEMENTED]  Campain: " . $event->getListId(), ['newsletter']);
    }

    /**
     * Find mailchimp list by mailchimp list ID
     *
     * @param string $mailchimpListId
     * @return \Newsletter\Model\Entity\NewsletterList
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     */
    protected function _findMailchimpList($mailchimpListId)
    {
        return $this->NewsletterLists
            ->findByMailchimpListId($mailchimpListId)
            ->firstOrFail();
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
            'merges.PHONE' => 'phone'
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
