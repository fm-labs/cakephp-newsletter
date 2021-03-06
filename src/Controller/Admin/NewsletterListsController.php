<?php
namespace Newsletter\Controller\Admin;

use Cake\Event\Event;
use Cake\Http\Exception\NotFoundException;

/**
 * NewsletterLists Controller
 *
 * @property \Newsletter\Model\Table\NewsletterListsTable $NewsletterLists
 */
class NewsletterListsController extends AppController
{
    /**
     * @var array
     */
    public $paginate = [
        'order' => ['NewsletterLists.title' => 'ASC'],
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

    /**
     * View method
     *
     * @param string|null $id Newsletter Reader id.
     * @return void
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        //$this->set('related', ['NewsletterMembers' => []]);
        //$this->set('entityOptions', ['contain' => ['NewsletterMembers']]);
        $this->Action->execute();
    }

    /**
     * Mailchimp config
     *
     * @param null|int $id List ID
     * @return void|null|\Cake\Http\Response
     */
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
    public function implementedEvents(): array
    {
        $events = parent::implementedEvents();
        $events['Admin.Action.Index.getRowActions'] = function (Event $event) {
            $event->result[] = [__d('newsletter', 'Mailchimp Hello'), ['action' => 'mailchimpHello', ':id']];
        };

        return $events;
    }
}
