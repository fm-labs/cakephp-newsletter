<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 6/5/16
 * Time: 1:02 AM
 */

namespace Newsletter\View\Cell;

use Cake\View\Cell;
use Newsletter\Model\Table\NewsletterMembersTable;

/**
 * Class NewsletterMembersAdminPanelCell
 *
 * @package Newsletter\View\Cell
 *
 * @property NewsletterMembersTable $NewsletterMembers
 */
class NewsletterMembersAdminPanelCell extends Cell
{
    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Default display method.
     *
     * @return void
     */
    public function display()
    {
        $this->loadModel('Newsletter.NewsletterMembers');

        $NewsletterMembers = $this->NewsletterMembers
            ->find()
            ->where(['NewsletterMembers.is_canceled' => false])
            ->contain([])
            ->limit(5)
            ->orderDesc('NewsletterMembers.id');

        $this->set('NewsletterMembers', $NewsletterMembers);
    }
}
