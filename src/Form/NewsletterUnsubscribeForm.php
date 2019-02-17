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
        $this->NewsletterMembers = TableRegistry::get('Newsletter.NewsletterMembers');
    }

    protected function _buildSchema(Schema $schema)
    {
        $schema->addField('email', $this->NewsletterMembers->schema()->column('email'));

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

        if ($member->errors()) {
            $this->_errors = $member->errors();

            return false;
        }

        return true;
    }
}
