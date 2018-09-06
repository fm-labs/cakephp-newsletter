<?php
namespace Newsletter\Controller\Admin;

use Backend\Controller\BackendActionsTrait;
use Cake\Event\Event;
use Newsletter\Mailer\NewsletterOwnerMailer;

/**
 * NewsletterMembers Controller
 *
 * @property \Newsletter\Model\Table\NewsletterMembersTable $NewsletterMembers
 */
class NewsletterMembersController extends AppController
{
    use BackendActionsTrait;

    /**
     * @var array
     */
    public $paginate = [
        'limit' => 100,
        'order' => ['NewsletterMembers.id' => 'DESC']
    ];

    /**
     * @var array
     */
    public $actions = [
        'index'     => 'Backend.Index',
        'view'      => 'Backend.View',
        'add'       => 'Backend.Add',
        'edit'      => 'Backend.Edit',
        'delete'    => 'Backend.Delete'
    ];
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
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->Action->execute();
    }

    /**
     * @param null $id
     */
    public function notifyOwner($id = null)
    {
        $NewsletterMember = $this->NewsletterMembers->get($id);
        if ((new NewsletterOwnerMailer())->send('subscriptionNotify', [$NewsletterMember])) {
            $this->Flash->success("Owner notification sent");
        } else {
            $this->Flash->error("Operation failed");
        }
        $this->redirect($this->referer(['action' => 'index']));
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Backend.Action.Index.getRowActions' => function(Event $event) {
                $event->result[] = [__('Notify Owner'), ['action' => 'notifyOwner', ':id']];
            }
        ];
    }
}
