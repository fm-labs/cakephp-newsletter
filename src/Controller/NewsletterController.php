<?php

namespace Newsletter\Controller;

use Cake\Core\Configure;
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

        $this->viewBuilder()->layout(Configure::read('Newsletter.layout'));
    }

    public function subscribe()
    {
        $success = false;
        $member = $this->NewsletterMembers->newEntity();
        if ($this->request->is(['post', 'put'])) {
            $email = $this->request->data('email');
            $member = $this->NewsletterMembers->subscribeMember($email, $this->request->data, ['events' => true, 'source' => 'form']);
            if ($member && !$member->errors() && $member->id) {
                $this->Flash->success(__d('newsletter', 'Newsletter signup was successful!'));
                $success = true;
            } else {
                $this->Flash->error(__d('newsletter', 'Please fill all required fields'));
            }
        }
        $this->set('member', $member);
        $this->set('success', $success);
        $this->set('_serialize', ['success', 'member']);
    }

    public function unsubscribe()
    {
        $success = false;
        $member = $this->NewsletterMembers->newEntity();
        if ($this->request->is(['post', 'put'])) {
            $email = $this->request->data('email');
            $member = $this->NewsletterMembers->unsubscribeMember($email, ['events' => true, 'source' => 'form']);
            if ($member && !$member->errors()) {
                $this->Flash->success(__d('newsletter', 'Unsubscribe was successful!'));
                $success = true;
            } else {
                $this->Flash->error(__d('newsletter', 'Please fill all required fields'));
            }
        }
        $this->set('member', $member);
        $this->set('success', $success);
        $this->set('_serialize', ['success', 'member']);
    }

//    public function subscribe()
//    {
//        $success = false;
//        $form = new NewsletterSubscribeForm();
//        if ($this->request->is(['post', 'put'])) {
//            if ($form->execute($this->request->data())) {
//                $this->Flash->success(__d('newsletter', 'Newsletter signup was successful!'));
//                $success = true;
//            } else {
//                $this->Flash->error(__d('newsletter', 'Please fill all required fields'));
//            }
//        }
//        $this->set('form', $form);
//        $this->set('success', $success);
//    }
//
//    public function unsubscribe()
//    {
//        $success = false;
//        $form = new NewsletterUnsubscribeForm();
//        if ($this->request->is(['post', 'put'])) {
//            if ($form->execute($this->request->data())) {
//                $this->Flash->success(__d('newsletter', 'Unsubscribe was successful!'));
//                $success = true;
//            } else {
//                $this->Flash->error(__d('newsletter', 'Please fill all required fields'));
//            }
//        }
//        $this->set('form', $form);
//        $this->set('success', $success);
//    }
}
