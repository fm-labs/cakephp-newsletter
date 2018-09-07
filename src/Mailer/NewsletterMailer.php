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
     * @var NewsletterMember
     */
    protected $_member;

    /**
     * @param Email|null $email
     */
    public function __construct(Email $email = null)
    {
        $localizedEmailClass = '\\Banana\\Mailer\\LocalizedEmail';
        if ($email === null && class_exists($localizedEmailClass)) {
            $email = new $localizedEmailClass();
        }

        parent::__construct($email);

        if (Configure::check('Newsletter.Mailer.profile')) {
            $this->profile(Configure::read('Newsletter.Mailer.profile'));
        }
    }

    /**
     * @param NewsletterMember $member
     * @return array
     */
    public function memberPending(NewsletterMember $member)
    {
        $this
            ->to($member->email)
            ->subject(__("Please confirm your newsletter subscription"))
            ->template('Newsletter.member_pending')
            ->profile(__FUNCTION__);

        $this->_setMember($member);
    }

    /**
     * @param NewsletterMember $member
     * @return array
     */
    public function memberSubscribe(NewsletterMember $member)
    {
        $this
            ->to($member->email)
            ->subject(__("Your newsletter subscription was successful"))
            ->template('Newsletter.member_subscribe')
            ->profile(__FUNCTION__);

        $this->_setMember($member);
    }

    /**
     * @param NewsletterMember $member
     * @return array
     */
    public function memberUnsubscribe(NewsletterMember $member)
    {
        $this
            ->to($member->email)
            ->subject(__("Unsubscribe confirmation"))
            ->template('Newsletter.member_unsubscribe')
            ->profile(__FUNCTION__);

        $this->_setMember($member);
    }

    /**
     * Overloading profile() method
     * to override profile from configuration
     */
    public function profile($profile = null)
    {
        if ($profile === null) {
            return $this->_email->profile();
        }

        if (is_string($profile) /*&& Configure::check('Newsletter.Email.' . $profile)*/) {
            $profile = (array) Configure::read('Newsletter.Email.' . $profile);
        }

        $this->_email->profile($profile);
        return $this;
    }


    /**
     * Sets the active member for emailing
     *
     * @param NewsletterMember $member
     * @return $this
     */
    protected function _setMember(NewsletterMember $member)
    {
        $this->_member = $member;

        $this->to($member->email);
        $this->set('member', $member);

        if (method_exists($this->_email, 'locale')) {
            //$this->locale($member->locale);
            $this->_email->locale($member->locale);
        }

        return $this;
    }
}
