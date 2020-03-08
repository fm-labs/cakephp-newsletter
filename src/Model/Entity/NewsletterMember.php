<?php
namespace Newsletter\Model\Entity;

use Cake\ORM\Entity;
use Cake\Utility\Inflector;

/**
 * NewsletterMember Entity.
 *
 * @property int $id
 * @property string $email
 * @property bool $is_email_verified
 * @property string $email_format
 * @property string $greeting
 * @property string $title
 * @property string $first_name
 * @property string $last_name
 * @property bool $is_canceled
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Cake\I18n\Time $canceled
 */
class NewsletterMember extends Entity
{

    protected static $_greetings;

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];

    protected $_virtual = [
        'name',
        'display_name',
    ];

    protected function _getName()
    {
        $name = null;
        $firstName = $this->first_name;
        $lastName = $this->last_name;

        if ($firstName && $lastName) {
            $name = $firstName . ' ' . $lastName;
        } elseif ($lastName) {
            $name = $lastName;
        } elseif ($firstName) {
            $name = $firstName;
        }

        return $name;
    }

    protected function _getDisplayName()
    {
        $name = null;
        $firstName = $this->first_name;
        $lastName = $this->last_name;

        if ($firstName && $lastName) {
        } elseif ($lastName) {
            $name = $lastName;
        } elseif ($firstName) {
            $name = $firstName;
        }

        if (!$lastName) {
            $lastName = __d('newsletter', '[UNKNOWN]');
        }

        if (!$firstName) {
            $firstName = __d('newsletter', '[UNKNOWN]');
        }

        return sprintf("%s, %s", $lastName, $firstName);
    }
}
