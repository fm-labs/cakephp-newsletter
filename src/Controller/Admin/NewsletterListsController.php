<?php
namespace Newsletter\Controller\Admin;

use Backend\Controller\BackendActionsTrait;
use Cake\Event\Event;
use Newsletter\Mailer\NewsletterOwnerMailer;

/**
 * NewsletterLists Controller
 *
 * @property \Newsletter\Model\Table\NewsletterListsTable $NewsletterLists
 */
class NewsletterListsController extends AppController
{
    use BackendActionsTrait;

    /**
     * @var array
     */
    public $paginate = [
        'order' => ['NewsletterLists.title' => 'ASC']
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
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Backend.Action.Index.getRowActions' => function(Event $event) {
                //$event->result[] = [__('Notify Owner'), ['action' => 'notifyOwner', ':id']];
            }
        ];
    }
}
