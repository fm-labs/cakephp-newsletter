<?php

namespace Newsletter\Mailer;

use Cake\Core\Configure;
use Cake\Mailer\Email;
use Cake\Mailer\Mailer;
use Newsletter\Model\Entity\NewsletterMember;

/**
 * Class NewsletterOwnerMailer
 *
 * @package Newsletter\Mailer
 */
class NewsletterOwnerMailer extends Mailer
{
    /**
     * @param Email|null $email
     */
    public function __construct(Email $email = null)
    {
        parent::__construct($email);

        $profile = (Configure::read('Newsletter.Email.ownerProfile')) ?: 'owner';
        $this->_email->profile($profile);
    }

    /**
     * @param NewsletterMember $subscriber
     * @return void
     */
    public function subscriptionNotify(NewsletterMember $subscriber)
    {
        $this
            ->subject("Neue Newsletter Anmeldung") // @todo i18n
            ->template('Newsletter.Newsletter/owner_signup_notify')
            ->set(compact('subscriber'));
    }
}
