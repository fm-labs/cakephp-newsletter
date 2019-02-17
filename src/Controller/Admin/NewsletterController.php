<?php
namespace Newsletter\Controller\Admin;

/**
 * Newsletter Controller
 */
class NewsletterController extends AppController
{
    public $modelClass = false;

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->redirect(['controller' => 'NewsletterLists']);
    }
}
