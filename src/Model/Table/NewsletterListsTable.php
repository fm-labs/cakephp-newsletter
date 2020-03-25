<?php
namespace Newsletter\Model\Table;

use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Mailchimp\Mailchimp\MailchimpApiClient;
use Newsletter\Model\Entity\NewsletterList;

/**
 * NewsletterLists Model
 *
 * @property \Cake\ORM\Association\HasMany $NewsletterMembers
 *
 * @method NewsletterList get($primaryKey, $options = [])
 * @method NewsletterList newEntity($data = null, array $options = [])
 * @method NewsletterList[] newEntities(array $data, array $options = [])
 * @method NewsletterList|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method NewsletterList patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method NewsletterList[] patchEntities($entities, array $data, array $options = [])
 * @method NewsletterList findOrCreate($search, callable $callback = null, $options = [])
 */
class NewsletterListsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('newsletter_lists');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

//        $this->hasMany('NewsletterMembers', [
//            'foreignKey' => 'newsletter_list_id',
//            'className' => 'Newsletter.NewsletterMembers'
//        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', 'create');

        $validator
            ->allowEmptyString('title');

        $validator
            ->boolean('mailchimp_enabled')
            ->requirePresence('mailchimp_enabled', 'create')
            ->notEmptyString('mailchimp_enabled');

        $validator
            ->allowEmptyString('mailchimp_listid');

        $validator
            ->allowEmptyString('mailchimp_apikey');

        return $validator;
    }

    /**
     * Find first record with given mailchimp list ID
     *
     * @param $mailchimpListId
     * @param array $options
     * @return \Cake\ORM\Query
     */
    public function findByMailchimpListId($mailchimpListId, array $options = [])
    {
        return $this->find('all', $options)
            ->where(['NewsletterLists.mailchimp_listid' => $mailchimpListId]);
    }

    public function getMailchimpClient(NewsletterList $list)
    {
        if (!$list->mailchimp_listid) {
            throw new \RuntimeException("Mailchimp List ID not configured");
        } elseif (!$list->mailchimp_apikey) {
            throw new \RuntimeException("Mailchimp API Key not configured");
        }

        $client = new MailchimpApiClient([
            'api_key' => $list->mailchimp_apikey,
        ]);

        return $client;
    }

    /**
     * Get first record with given mailchimp list ID
     *
     * @param $mailchimpListId
     * @param array $options
     * @return null|\Newsletter\Model\Entity\NewsletterList
     */
    public function getByMailchimpListId($mailchimpListId, array $options = [])
    {
        return $this->findByMailchimpListId($mailchimpListId)->first();
    }

//    /**
//     * Get first member of given list by email address
//     *
//     * @param NewsletterList $list
//     * @param $email
//     * @param array $options
//     * @return null|\Newsletter\Model\Entity\NewsletterMember
//     */
//    public function getListMemberByEmail(NewsletterList $list, $email, array $options = [])
//    {
//        return $this->NewsletterMembers->find('all', $options)
//            ->where([
//                'newsletter_list_id' => $list->id,
//                'email' => $email
//            ])
//            ->first();
//    }
//
//    /**
//     * Subscribe to list with email address
//     *
//     * @param NewsletterList $list
//     * @param $email
//     * @param array $data
//     * @param array $options
//     * @return bool|\Newsletter\Model\Entity\NewsletterMember
//     */
//    public function subscribeMember(NewsletterList $list, $email, $data = [], array $options = [])
//    {
//        $member = $this->getListMemberByEmail($list, $email);
//        if ($member && $member->status == NewsletterMembersTable::STATUS_SUBSCRIBED) {
//            return $this->updateMember($list, $email, $data, $options);
//        }
//
//        $options += ['optIn' => null, 'events' => null];
//        if ($options['events'] === true) {
//            $options['events'] = ['before', 'after'];
//        }
//
//        $isNew = false;
//        if (!$member) {
//            $member = $this->NewsletterMembers->newEntity([
//                'email' => $email,
//                'newsletter_list_id' => $list->id,
//                'email_verified' => !$options['optIn']
//            ]);
//            $isNew = true;
//        }
//
//        if (!empty($data)) {
//            $member = $this->NewsletterMembers->patchEntity($member, $data);
//        }
//
//        // Dispatch 'beforeSubscribe' event
//        if ($options['events'] && in_array('before', $options['events'])) {
//            $event = new Event('Newsletter.List.Member.beforeSubscribe', $this, [
//                'new' => $isNew,
//                'list' => $list,
//                'member' => $member,
//                'data' => $data,
//                'options' => $options
//            ]);
//            $this->getEventManager()->dispatch($event);
//        }
//
//        // Update entity
//        if ($options['optIn'] == true && $member->email_verified == false) {
//            $member->status = NewsletterMembersTable::STATUS_PENDING;
//        } else {
//            $member->status = NewsletterMembersTable::STATUS_SUBSCRIBED;
//            $member->email_verified = true;
//        }
//
//        if (!$this->NewsletterMembers->save($member)) {
//            return $member;
//        }
//
//        // Dispatch 'afterSubscribe' event
//        if ($options['events'] && in_array('after', $options['events'])) {
//            $event = new Event('Newsletter.List.Member.afterSubscribe', $this, [
//                'new' => $isNew,
//                'list' => $list,
//                'member' => $member,
//                'data' => $data,
//                'options' => $options
//            ]);
//            $this->getEventManager()->dispatch($event);
//        }
//
//        return $member;
//    }
//
//    /**
//     * Unsubscribe from list with email address
//     *
//     * @param NewsletterList $list
//     * @param $email
//     * @param array $options
//     * @return bool|\Newsletter\Model\Entity\NewsletterMember
//     */
//    public function unsubscribeMember(NewsletterList $list, $email, array $options = [])
//    {
//        $options += ['events' => null];
//        if ($options['events'] === true) {
//            $options['events'] = ['before', 'after'];
//        }
//
//        $member = $this->getListMemberByEmail($list, $email);
//        if (!$member || $member->status == NewsletterMembersTable::STATUS_UNSUBSCRIBED || $member->status == NewsletterMembersTable::STATUS_CLEANED) {
//            //throw new NotFoundException("Unknown subscriber email: $email");
//            return true;
//        }
//
//        // Dispatch 'beforeSubscribe' event
//        if ($options['events'] && in_array('before', $options['events'])) {
//            $event = new Event('Newsletter.List.Member.beforeUnsubscribe', $this, [
//                'list' => $list,
//                'member' => $member,
//                'options' => $options
//            ]);
//            $this->getEventManager()->dispatch($event);
//        }
//
//        // Update entity
//        $member->status = NewsletterMembersTable::STATUS_UNSUBSCRIBED;
//
//        if (!$this->NewsletterMembers->save($member)) {
//            return false;
//        }
//
//        // Dispatch 'afterUnsubscribe' event
//        if ($options['events'] && in_array('after', $options['events'])) {
//            $event = new Event('Newsletter.List.Member.afterUnsubscribe', $this, [
//                'list' => $list,
//                'member' => $member,
//                'options' => $options
//            ]);
//            $this->getEventManager()->dispatch($event);
//        }
//
//        return $member;
//    }
//
//    /**
//     * Update list member data by email
//     *
//     * @param NewsletterList $list
//     * @param $email
//     * @param array $data
//     * @param array $options
//     * @return bool|\Newsletter\Model\Entity\NewsletterMember
//     */
//    public function updateMember(NewsletterList $list, $email, $data = [], array $options = [])
//    {
//        $options += ['events' => null];
//        if ($options['events'] === true) {
//            $options['events'] = ['before', 'after'];
//        }
//
//        $member = $this->getListMemberByEmail($list, $email);
//        if (!$member) {
//            return $this->subscribeMember($list, $email, $data, $options);
//        }
//
//        //@TODO Set allowed fields
//        //$member->accessible([], false);
//        $member = $this->NewsletterMembers->patchEntity($member, $data);
//
//        // Dispatch 'beforeUpdate' event
//        if ($options['events'] && in_array('before', $options['events'])) {
//            $event = new Event('Newsletter.List.Member.beforeUpdate', $this, [
//                'list' => $list,
//                'member' => $member,
//                'data' => $data,
//                'options' => $options
//            ]);
//            $this->getEventManager()->dispatch($event);
//        }
//
//        if (!($member = $this->NewsletterMembers->save($member))) {
//            return false;
//        }
//
//        // Dispatch 'afterUpdate' event
//        if ($options['events'] && in_array('after', $options['events'])) {
//            $event = new Event('Newsletter.List.Member.afterUpdate', $this, [
//                'list' => $list,
//                'member' => $member,
//                'data' => $data,
//                'options' => $options
//            ]);
//            $this->getEventManager()->dispatch($event);
//        }
//
//        return $member;
//    }
}
