<?php

namespace Newsletter\Mailer;

use Cake\Core\Configure;
use Cake\Mailer\Email;
use Cake\Mailer\Mailer;
use Newsletter\Model\Entity\NewsletterMember;

/**
 * Class NewsletterMailer
 *
 * @package Newsletter\Mailer
 */
class NewsletterMailer extends Mailer
{
    /**
     * @param Email|null $email
     */
    public function __construct(Email $email = null)
    {
        parent::__construct($email);

        if (Configure::check('Newsletter.Email.profile')) {
            $this->_email->profile(Configure::read('Newsletter.Email.profile'));
        }
    }

    /**
     * @param NewsletterMember $subscriber
     * @return array
     */
    public function subscriptionConfirmation(NewsletterMember $subscriber)
    {
        $this
            ->subject("Ihre Newsletter Anmeldung") // @todo i18n
            ->to($subscriber->email)
            ->template('Newsletter.Newsletter/user_signup_notify')
            ->set(compact('subscriber'));
    }
}
