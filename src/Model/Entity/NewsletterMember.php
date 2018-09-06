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
        'display_name'
    ];

    protected function _getName()
    {
        $name = null;
        $first_name = $this->first_name;
        $last_name = $this->last_name;

        if ($first_name && $last_name) {
            $name = $first_name . ' ' . $last_name;
        } elseif ($last_name) {
            $name = $last_name;
        } elseif ($first_name) {
            $name = $first_name;
        }

        return $name;
    }

    protected function _getDisplayName()
    {
        $name = null;
        $first_name = $this->first_name;
        $last_name = $this->last_name;

        if ($first_name && $last_name) {
        } elseif ($last_name) {
            $name = $last_name;
        } elseif ($first_name) {
            $name = $first_name;
        }

        if (!$last_name) {
            $last_name = __d('newsletter', '[UNKNOWN]');
        }

        if (!$first_name) {
            $first_name = __d('newsletter', '[UNKNOWN]');
        }

        return sprintf("%s, %s", $last_name, $first_name);
    }
}
