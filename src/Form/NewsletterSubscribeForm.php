<?php
namespace Newsletter\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * class NewsletterSubscribeForm
 *
 * @property \Newsletter\Model\Table\NewsletterMembersTable $NewsletterMembers
 */
class NewsletterSubscribeForm extends Form
{

    public function __construct()
    {
        $this->NewsletterMembers = TableRegistry::getTableLocator()->get('Newsletter.NewsletterMembers');
    }

    protected function _buildSchema(Schema $schema)
    {
        foreach ($this->NewsletterMembers->schema()->columns() as $column) {
            $schema->addField($column, $this->NewsletterMembers->schema()->column($column));
        }

        return $schema;
    }

    protected function _buildValidator(Validator $validator)
    {
        $this->NewsletterMembers->validationDefault($validator);

        return $validator;
    }

    protected function _execute(array $data)
    {
        $email = $data['email'];
        $member = $this->NewsletterMembers->subscribeMember($email, $data, ['events' => true, 'source' => 'form']);
        if (!$member) {
            return false;
        }

        if ($member->getErrors() || !$member->id) {
            $this->_errors = $member->getErrors();

            return false;
        }

        return true;
    }
}
