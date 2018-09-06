<?php

namespace Newsletter\Controller;

use Cake\Event\Event;
use Newsletter\Model\Table\NewsletterMembersTable;

/**
 * Class NewsletterController
 * @package Newsletter\Controller
 *
 * @property NewsletterMembersTable $NewsletterMembers
 */
class NewsletterController extends AppController
{
    public $modelClass = "Newsletter.NewsletterMembers";

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        if ($this->components()->has('Auth')) {
            $this->Auth->allow(['subscribe', 'unsubscribe']);
        }
    }

    public function subscribe()
    {
        $member = $this->NewsletterMembers->newEntity();

        if ($this->request->is(['post', 'put'])) {
            $email = $this->request->data('email');
            $member = $this->NewsletterMembers->subscribe($email, $this->request->data);
            if ($member && !$member->errors() && $member->id) {
                $this->Flash->success(__d('newsletter', 'Newsletter signup was successful!'));
            } else {
                $this->Flash->error(__d('newsletter', 'Something went wrong. Please try again.'));
            }
        }
        $this->set('member', $member);
    }

    public function unsubscribe()
    {
        $member = $this->NewsletterMembers->newEntity();

        if ($this->request->is(['post', 'put'])) {
            $email = $this->request->data('email');
            $member = $this->NewsletterMembers->unsubscribe($email);
            if ($member && !$member->errors()) {
                $this->Flash->success(__d('newsletter', 'Newsletter signup was successful!'));
            } else {
                $this->Flash->error(__d('newsletter', 'Something went wrong. Please try again.'));
            }
        }
        $this->set('member', $member);
    }

}