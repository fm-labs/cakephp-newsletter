<?php
namespace Newsletter\Controller\Admin;

use Cake\Event\Event;
use Newsletter\Mailer\NewsletterMailer;
use Newsletter\Mailer\NewsletterOwnerMailer;

/**
 * NewsletterMembers Controller
 *
 * @property \Newsletter\Model\Table\NewsletterMembersTable $NewsletterMembers
 */
class NewsletterMembersController extends AppController
{
    /**
     * @var array
     */
    public $paginate = [
        'limit' => 100,
        'order' => ['NewsletterMembers.id' => 'DESC'],
    ];

    /**
     * @var array
     */
    public $actions = [
        'index' => 'Admin.Index',
        'view' => 'Admin.View',
        'add' => 'Admin.Add',
        'edit' => 'Admin.Edit',
        'delete' => 'Admin.Delete',
    ];

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Action->registerInline('sendEmail', ['scope' => ['form', 'table'], 'attrs' => ['data-icon' => 'envelope-o']]);
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
       // $this->set('fields.whitelist', ['email', 'status', 'created']);
        $this->set('fields.blacklist', ['greeting', 'title', 'created', 'modified']);
        $this->Action->execute();
    }

    /**
     * View method
     *
     * @param string|null $id Newsletter Reader id.
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->Action->execute();
    }

    public function add($id = null)
    {
        $this->set('emailFormats', $this->NewsletterMembers->listEmailFormats());
        $this->set('statuses', $this->NewsletterMembers->listStatuses());
        $this->Action->execute();
    }

    public function edit($id = null)
    {
        $this->set('emailFormats', $this->NewsletterMembers->listEmailFormats());
        $this->set('statuses', $this->NewsletterMembers->listStatuses());
        $this->Action->execute();
    }

    /**
     * @param null $id
     */
    public function sendEmail($id = null)
    {
        $mailerActions = [
            'memberSubscribe' => 'Member Subscribe',
            'memberUnsubscribe' => 'Member Unsubscribe',
            'memberPending' => 'Member Pending',
        ];
        $member = $this->NewsletterMembers->get($id);
        if ($this->request->is(['post'])) {
            if ((new NewsletterMailer())->send($this->request->getData('mailer_action'), [$member])) {
                $this->Flash->success("Email sent");
            } else {
                $this->Flash->error("Operation failed");
            }
            //$this->redirect($this->referer(['action' => 'index']));
        }

        $this->set(compact('member', 'mailerActions'));
    }

/**
 * @return array
 */
//    public function implementedEvents(): array
//    {
//        return [
//            'Admin.Action.Index.getRowActions' => function(Event $event) {
//                $event->result[] = [__d('newsletter', 'Notify Owner'), ['action' => 'notifyOwner', ':id']];
//            }
//        ];
//    }
}
