<?php
namespace Newsletter\Controller\Admin;

use Backend\Controller\BackendActionsTrait;
use Cake\Event\Event;
use Cake\Network\Exception\NotFoundException;

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

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        //$this->Action->registerInline('mailchimpConfig', ['scope' => ['form', 'table'], 'attrs' => ['data-icon' => 'monkey']]);
    }

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
        $this->set('related', ['NewsletterMembers' => []]);
        $this->set('entityOptions', ['contain' => ['NewsletterMembers']]);
        $this->Action->execute();
    }

    public function mailchimpConfig($id = null)
    {
        try {

            $list = $this->NewsletterLists->get($id);
            if (!$list) {
                throw new NotFoundException();
            }

            $mailchimp = $this->NewsletterLists->getMailchimpClient($list);

            $mcMembers = $mailchimp->getListMembers($list->mailchimp_listid);
            debug($mcMembers);
            //$mcSubmitForms = $mailchimp->getListSignupForms($list->mailchimp_listid);
            //debug($mcSubmitForms);

        } catch (\Exception $ex) {
            $this->Flash->error($ex->getMessage());
            return $this->redirect($this->referer(['action' => 'index']));
        }
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        $events = parent::implementedEvents();
        $events['Backend.Action.Index.getRowActions'] = function(Event $event) {
            $event->result[] = [__('Mailchimp Hello'), ['action' => 'mailchimpHello', ':id']];
        };
        return $events;
    }
}
