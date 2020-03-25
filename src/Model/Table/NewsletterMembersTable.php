<?php
namespace Newsletter\Model\Table;

use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Mailchimp\Mailchimp\MailchimpApiClient;
use Newsletter\Model\Entity\NewsletterMember;

/**
 * NewsletterMembers Model
 *
 */
class NewsletterMembersTable extends Table
{
    const STATUS_SUBSCRIBED = 'SUB';
    const STATUS_UNSUBSCRIBED = 'UNSUB';
    const STATUS_PENDING = 'PEND';
    const STATUS_CLEANED = 'CLEANED';

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('newsletter_members');
        $this->setDisplayField('email');
        $this->setPrimaryKey('id');

        //$this->belongsTo('NewsletterLists', [
        //    'className' => 'Newsletter.NewsletterLists',
        //    'foreignKey' => 'newsletter_list_id'
        //]);

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmptyString('id', 'create');

        //$validator
        //    ->add('newsletter_list_id', 'valid', ['rule' => 'numeric'])
        //    ->notEmptyString('newsletter_list_id');

        $validator
            ->add('email', 'email_check', ['rule' => ['email', true], 'message' => __d('newsletter', 'Provide a valid email address'), 'on' => 'create'])
            ->add('email', 'email_nocheck', ['rule' => ['email', false], 'message' => __d('newsletter', 'Provide a valid email address'), 'on' => 'update'])
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->add('email_verified', 'valid', ['rule' => 'boolean'])
            ->allowEmptyString('email_verified');

        $validator
            ->allowEmptyString('email_format');

        $validator
            ->allowEmptyString('greeting');

        $validator
            ->allowEmptyString('title');

        $validator
            ->allowEmptyString('first_name');

        $validator
            ->allowEmptyString('last_name');

        $validator
            ->allowEmptyString('status');

        $validator
            ->allowEmptyString('locale');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));
        //$rules->add($rules->existsIn(['newsletter_list_id'], 'NewsletterLists'));
        return $rules;
    }

    /**
     * Email address finder method
     * @return \Cake\ORM\Query
     */
    public function findByEmail($email)
    {
        return $this->find()
            ->where(['email' => $email]);
    }

    /**
     * Get first member of given list by email address
     *
     * @param $email
     * @param array $options
     * @return null|\Newsletter\Model\Entity\NewsletterMember
     */
    public function getByEmail($email, array $options = [])
    {
        return $this->find('all', $options)
            ->where([
                'email' => $email,
            ])
            ->first();
    }

    /**
     * Subscribe to list with email address
     *
     * @param $email
     * @param array $data
     * @param array $options
     * @return bool|\Newsletter\Model\Entity\NewsletterMember
     */
    public function subscribeMember($email, $data = [], array $options = [])
    {
        $member = $this->getByEmail($email);
        if ($member && $member->status == self::STATUS_SUBSCRIBED) {
            return $this->updateMember($email, $data, $options);
        }

        $options += ['optIn' => null, 'events' => null];
        if ($options['events'] === true) {
            $options['events'] = ['before', 'after'];
        }

        $isNew = false;
        if (!$member) {
            $member = $this->newEntity([
                'email' => $email,
                'email_verified' => !$options['optIn'],
            ]);
            $isNew = true;
            //debug($member->getErrors());
        }

        if (!empty($data)) {
            $member = $this->patchEntity($member, $data);
            //debug($member->getErrors());
        }

        // Dispatch 'beforeSubscribe' event
        if ($options['events'] && in_array('before', $options['events'])) {
            $event = new Event('Newsletter.Model.Member.beforeSubscribe', $this, [
                'new' => $isNew,
                'member' => $member,
                'data' => $data,
                'options' => $options,
            ]);
            $this->getEventManager()->dispatch($event);
        }

        // Update entity
        if ($options['optIn'] == true && $member->email_verified == false) {
            $member->status = self::STATUS_PENDING;
        } else {
            $member->status = self::STATUS_SUBSCRIBED;
            $member->email_verified = true;
        }

        if (!$this->save($member)) {
            //debug("saving failed");
            return $member;
        }

        // Dispatch 'afterSubscribe' event
        if ($options['events'] && in_array('after', $options['events'])) {
            $event = new Event('Newsletter.Model.Member.afterSubscribe', $this, [
                'new' => $isNew,
                'member' => $member,
                'data' => $data,
                'options' => $options,
            ]);
            $this->getEventManager()->dispatch($event);
        }

        return $member;
    }

    /**
     * Unsubscribe from list with email address
     *
     * @param $email
     * @param array $options
     * @return bool|\Newsletter\Model\Entity\NewsletterMember
     */
    public function unsubscribeMember($email, array $options = [])
    {
        $options += ['events' => null];
        if ($options['events'] === true) {
            $options['events'] = ['before', 'after'];
        }

        $member = $this->getByEmail($email);
        if (!$member) {
            $member = $this->newEntity(['email' => $email]);

            return $member;
        }

        if ($member->status == self::STATUS_UNSUBSCRIBED || $member->status == self::STATUS_CLEANED) {
            return $member;
        }

        // Dispatch 'beforeSubscribe' event
        if ($options['events'] && in_array('before', $options['events'])) {
            $event = new Event('Newsletter.Model.Member.beforeUnsubscribe', $this, [
                'member' => $member,
                'options' => $options,
            ]);
            $this->getEventManager()->dispatch($event);
        }

        // Update entity
        $member->status = self::STATUS_UNSUBSCRIBED;

        if (!$this->save($member)) {
            return $member;
        }

        // Dispatch 'afterUnsubscribe' event
        if ($options['events'] && in_array('after', $options['events'])) {
            $event = new Event('Newsletter.Model.Member.afterUnsubscribe', $this, [
                'member' => $member,
                'options' => $options,
            ]);
            $this->getEventManager()->dispatch($event);
        }

        return $member;
    }

    /**
     * Update list member data by email
     *
     * @param $email
     * @param array $data
     * @param array $options
     * @return bool|\Newsletter\Model\Entity\NewsletterMember
     */
    public function updateMember($email, $data = [], array $options = [])
    {
        $options += ['events' => null];
        if ($options['events'] === true) {
            $options['events'] = ['before', 'after'];
        }

        $member = $this->getByEmail($email);
        if (!$member) {
            return $this->subscribeMember($email, $data, $options);
        }

        //@TODO Set allowed fields
        //$member->accessible([], false);
        $member = $this->patchEntity($member, $data);

        // Dispatch 'beforeUpdate' event
        if ($options['events'] && in_array('before', $options['events'])) {
            $event = new Event('Newsletter.Model.Member.beforeUpdate', $this, [
                'member' => $member,
                'data' => $data,
                'options' => $options,
            ]);
            $this->getEventManager()->dispatch($event);
        }

        if (!$this->save($member)) {
            return $member;
        }

        // Dispatch 'afterUpdate' event
        if ($options['events'] && in_array('after', $options['events'])) {
            $event = new Event('Newsletter.Model.Member.afterUpdate', $this, [
                'member' => $member,
                'data' => $data,
                'options' => $options,
            ]);
            $this->getEventManager()->dispatch($event);
        }

        return $member;
    }

    public function listStatuses()
    {
        return [
            self::STATUS_UNSUBSCRIBED => __d('newsletter', 'Unsubscribed'),
            self::STATUS_SUBSCRIBED => __d('newsletter', 'Subscribed'),
            self::STATUS_CLEANED => __d('newsletter', 'Cleaned'),
            self::STATUS_PENDING => __d('newsletter', 'Pending'),
        ];
    }

    public function listEmailFormats()
    {
        return [
            'html' => 'Html',
            'text' => 'Text',
        ];
    }

    /**
     * @return \Mailchimp\Mailchimp\MailchimpApiClient
     */
    public function mailchimp()
    {
        if (!Plugin::isLoaded('Mailchimp')) {
            throw new MissingPluginException(['plugin' => 'Mailchimp']);
        }

        $mailchimp = new MailchimpApiClient(Configure::read('Newsletter.Mailchimp'));

        return $mailchimp;
    }

    public function mailchimpSync(NewsletterMember $member)
    {
        try {
            $mailchimp = $this->mailchimp();

            $mcMember = $mailchimp->getMember($member->email);

            switch ($member->status) {
                case self::STATUS_PENDING:
                    // we do not sync pending state yet
                    break;
                case self::STATUS_SUBSCRIBED:
                    if (!$mcMember || $mcMember['status'] != MailchimpApiClient::MEMBER_STATUS_SUBSCRIBED) {
                        Log::info("MailchimpSync: REMOTE: subscribe " . $member->email, ['newsletter']);
                        $mailchimp->subscribeMember($member->email, []);
                    }
                    break;
                case self::STATUS_CLEANED:
                case self::STATUS_UNSUBSCRIBED:
                    if ($mcMember) {
                        Log::info("MailchimpSync: REMOTE: unsubscribe " . $member->email, ['newsletter']);
                        $mailchimp->unsubscribeMember($member->email);
                    }
                    break;
            }
        } catch (\Exception $ex) {
            Log::error("MailchimpSync: ERROR: " . $ex->getMessage(), ['newsletter']);
        }
    }

    public function getStats()
    {
        $stats = [
            'newsletter_subscribers_total_count' => 0,
            'newsletter_subscribers_today_count' => 0,
            'newsletter_subscribers_week_count' => 0,
            'newsletter_subscribers_month_count' => 0,
        ];

        $stats['newsletter_subscribers_total_count'] = $this->find()->count();
        $stats['newsletter_subscribers_today_count'] = $this->find()->where(['created >= '  => date("Y-m-d H:i:s", time() - DAY)])->count();
        $stats['newsletter_subscribers_week_count'] = $this->find()->where(['created >= '  => date("Y-m-d H:i:s", time() - WEEK)])->count();
        $stats['newsletter_subscribers_month_count'] = $this->find()->where(['created >= '  => date("Y-m-d H:i:s", time() - MONTH)])->count();

        return $stats;
    }
}
