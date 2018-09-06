<?php
namespace Newsletter\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * NewsletterLists Model
 *
 * @property \Cake\ORM\Association\HasMany $NewsletterMembers
 *
 * @method \Newsletter\Model\Entity\NewsletterList get($primaryKey, $options = [])
 * @method \Newsletter\Model\Entity\NewsletterList newEntity($data = null, array $options = [])
 * @method \Newsletter\Model\Entity\NewsletterList[] newEntities(array $data, array $options = [])
 * @method \Newsletter\Model\Entity\NewsletterList|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Newsletter\Model\Entity\NewsletterList patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Newsletter\Model\Entity\NewsletterList[] patchEntities($entities, array $data, array $options = [])
 * @method \Newsletter\Model\Entity\NewsletterList findOrCreate($search, callable $callback = null, $options = [])
 */
class NewsletterListsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('newsletter_lists');
        $this->displayField('title');
        $this->primaryKey('id');

        $this->hasMany('NewsletterMembers', [
            'foreignKey' => 'newsletter_list_id',
            'className' => 'Newsletter.NewsletterMembers'
        ]);
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
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('title');

        $validator
            ->boolean('mailchimp_enabled')
            ->requirePresence('mailchimp_enabled', 'create')
            ->notEmpty('mailchimp_enabled');

        $validator
            ->allowEmpty('mailchimp_listid');

        $validator
            ->allowEmpty('mailchimp_apikey');

        return $validator;
    }
}
