<?php
namespace Newsletter\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * class NewsletterSubscribeForm
 *
 * @property \Newsletter\Model\Table\NewsletterMembersTable $NewsletterMembers
 */
class NewsletterUnsubscribeForm extends Form
{

    public function __construct()
    {
        $this->NewsletterMembers = TableRegistry::getTableLocator()->get('Newsletter.NewsletterMembers');
    }

    protected function _buildSchema(Schema $schema)
    {
        $schema->addField('email', $this->NewsletterMembers->getSchema()->column('email'));

        return $schema;
    }

    protected function _buildValidator(Validator $validator)
    {
        $validator->email('email', false);

        return $validator;
    }

    protected function _execute(array $data)
    {
        $email = $data['email'];
        $member = $this->NewsletterMembers->unsubscribeMember($email, ['events' => true, 'source' => 'form']);
        if (!$member) {
            return false;
        }

        if ($member->getErrors()) {
            $this->_errors = $member->getErrors();

            return false;
        }

        return true;
    }
}
