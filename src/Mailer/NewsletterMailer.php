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
            $this->setProfile(Configure::read('Newsletter.Mailer.profile'));
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
            ->setSubject(__d('newsletter', "Please confirm your newsletter subscription"))
            ->setTemplate('Newsletter.member_pending')
            ->setProfile(__FUNCTION__);

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
            ->setSubject(__d('newsletter', "Your newsletter subscription was successful"))
            ->setTemplate('Newsletter.member_subscribe')
            ->setProfile(__FUNCTION__);

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
            ->setSubject(__d('newsletter', "Unsubscribe confirmation"))
            ->setTemplate('Newsletter.member_unsubscribe')
            ->setProfile(__FUNCTION__);

        $this->_setMember($member);
    }

    /**
     * Overloading profile() method
     * to override profile from configuration
     */
    public function setProfile($profile)
    {
        if (is_string($profile) /*&& Configure::check('Newsletter.Email.' . $profile)*/) {
            $profile = (array)Configure::read('Newsletter.Email.' . $profile);
        }

        $this->_email->setProfile($profile);

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
