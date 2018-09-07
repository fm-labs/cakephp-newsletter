<?php
namespace Newsletter\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * @deprecated Use NewsletterSubscribeForm
 */
class NewsletterSignupForm extends Form
{

    protected function _buildSchema(Schema $schema)
    {
        return $schema
            ->addField('greeting', 'string')
            ->addField('title', 'string')
            ->addField('first_name', 'string')
            ->addField('name', 'string')
            ->addField('email', ['type' => 'string']);
    }

    protected function _buildValidator(Validator $validator)
    {
        return $validator->add('name', 'notblank', [
            'rule' => 'notBlank',
            'message' => 'A name is required'
        ])->add('email', 'format', [
            'rule' => 'email',
            'message' => 'A valid email address is required',
        ]);
    }

    protected function _execute(array $data)
    {

        $NewsletterMembers = TableRegistry::get('Newsletter.NewsletterMembers');

        $member = $NewsletterMembers->newEntity($data);
        if ($member->errors()) {
            debug ($member);
            return false;
        }

        Log::info(sprintf("Newsletter-Anmeldung: %s|%s|%s|%s",
            $data['email'], $data['greeting'], $data['first_name'], $data['last_name']), ['newsletter']);

        // Email to User
        try {
            $email = new Email('newsletter');
            $email->subject("Neue Newsletter Anmeldung");
            $email->template('Newsletter.owner_member_signup_complete');
            $email->viewVars($member);
            $sent = $email->send();
            debug($sent);
            Log::info("Email: Owner Newsletter Signup notification has been sent", ['mail', 'newsletter']);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage(), ['mail', 'newsletter']);
        }

        // Email to User
        try {
            $email = new Email('default');
            $email->subject("Newsletter Anmeldung");
            $email->to($member['email']);
            $email->template('Newsletter.user_member_signup_complete');
            $email->viewVars($member);
            $sent = $email->send();
            debug($sent);
            Log::info("Email: User Newsletter Signup notification has been sent", ['mail', 'newsletter']);
            return $sent;
        } catch (\Exception $ex) {
            Log::error($ex->getMessage(), ['mail', 'newsletter']);
        }

        return false;
    }
}