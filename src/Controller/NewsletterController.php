<?php

namespace Newsletter\Controller;

use Cake\Event\Event;
use Newsletter\Form\NewsletterSubscribeForm;
use Newsletter\Form\NewsletterUnsubscribeForm;

/**
 * Class NewsletterController
 * @package Newsletter\Controller
 *
 * @property \Newsletter\Model\Table\NewsletterMembersTable $NewsletterMembers
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
        $form = new NewsletterSubscribeForm();
        if ($this->request->is(['post', 'put'])) {
            if ($form->execute($this->request->data())) {
                $this->Flash->success(__d('newsletter', 'Newsletter signup was successful!'));
            } else {
                $this->Flash->error(__d('newsletter', 'Something went wrong. Please try again.'));
            }
        }
        $this->set('form', $form);
    }

    public function unsubscribe()
    {
        $form = new NewsletterUnsubscribeForm();
        if ($this->request->is(['post', 'put'])) {
            if ($form->execute($this->request->data())) {
                $this->Flash->success(__d('newsletter', 'Unsubscribe was successful!'));
            } else {
                $this->Flash->error(__d('newsletter', 'Something went wrong. Please try again.'));
            }
        }
        $this->set('form', $form);
    }

//    public function subscribe()
//    {
//        $member = $this->NewsletterMembers->newEntity();
//        if ($this->request->is(['post', 'put'])) {
//            $email = $this->request->data('email');
//            $member = $this->NewsletterMembers->subscribeMember($email, $this->request->data, ['events' => true, 'source' => 'form']);
//            if ($member && !$member->errors() && $member->id) {
//                $this->Flash->success(__d('newsletter', 'Newsletter signup was successful!'));
//            } else {
//                $this->Flash->error(__d('newsletter', 'Something went wrong. Please try again.'));
//            }
//        }
//        $this->set('member', $member);
//    }
//
//    public function unsubscribe()
//    {
//        $member = $this->NewsletterMembers->newEntity();
//        if ($this->request->is(['post', 'put'])) {
//            $email = $this->request->data('email');
//            if ($this->NewsletterMembers->unsubscribeMember($email, ['events' => true, 'source' => 'form'])) {
//                $this->Flash->success(__d('newsletter', 'Unsubscribe was successful!'));
//            } else {
//                $this->Flash->error(__d('newsletter', 'Something went wrong. Please try again.'));
//            }
//        }
//        $this->set('member', $member);
//    }
}