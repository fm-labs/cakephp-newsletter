<?php
namespace Newsletter\Model\Table;

use Cake\Event\Event;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
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

        $this->table('newsletter_members');
        $this->displayField('email');
        $this->primaryKey('id');

        $this->belongsTo('NewsletterLists', [
            'className' => 'Newsletter.NewsletterLists',
            'foreignKey' => 'newsletter_list_id'
        ]);

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
            ->allowEmpty('id', 'create');

        $validator
            ->add('newsletter_list_id', 'valid', ['rule' => 'numeric'])
            ->notEmpty('newsletter_list_id');

        $validator
            ->add('email', 'valid', ['rule' => 'email'])
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->add('email_verified', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('email_verified');

        $validator
            ->allowEmpty('email_format');

        $validator
            ->allowEmpty('greeting');

        $validator
            ->allowEmpty('title');

        $validator
            ->allowEmpty('first_name');

        $validator
            ->allowEmpty('last_name');

        $validator
            ->allowEmpty('status');

        $validator
            ->allowEmpty('locale');

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
        $rules->add($rules->existsIn(['newsletter_list_id'], 'NewsletterLists'));
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

}
