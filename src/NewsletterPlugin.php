<?php

namespace Newsletter;

use Backend\Backend;
use Backend\BackendPluginInterface;
use Banana\Application;
use Banana\Plugin\BasePlugin;
use Banana\Plugin\PluginInterface;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\RouteBuilder;
use Newsletter\Service\MailchimpService;
use Newsletter\Service\NewsletterMailerService;

/**
 * Class NewsletterPlugin
 *
 * @package Newsletter
 */
class NewsletterPlugin extends BasePlugin implements EventListenerInterface
{
    protected $_name = "Newsletter";

    public function implementedEvents()
    {
        return [
            'Backend.Menu.build.admin_primary'    => ['callable' => 'buildBackendMenu', 'priority' => 80 ],
        ];
    }

    /**
     * @param Event $event
     * @return void
     */
    public function buildBackendMenu(Event $event)
    {
        $event->getSubject()->addItem([
            'title' => 'Newsletter',
            'url' => ['plugin' => 'Newsletter', 'controller' => 'NewsletterMembers', 'action' => 'index'],
            'data-icon' => 'newspaper-o',
//            'children' => [
//                'newsletter_lists' => [
//                    'title' => 'Newsletter Lists',
//                    'url' => ['plugin' => 'Newsletter', 'controller' => 'NewsletterLists', 'action' => 'index'],
//                    'data-icon' => 'list',
//                ],
//                'newsletter_members' => [
//                    'title' => 'Newsletter Members',
//                    'url' => ['plugin' => 'Newsletter', 'controller' => 'NewsletterMembers', 'action' => 'index'],
//                    'data-icon' => 'users',
//                ]
//            ]
        ]);
    }

    public function bootstrap(Application $app)
    {
        parent::bootstrap($app);

        EventManager::instance()->on($this);

        if (Configure::read('Newsletter.Mailer.enabled') == true) {
            EventManager::instance()->on(new NewsletterMailerService());
        }

        if (Configure::read('Newsletter.Mailchimp.enabled') == true && Plugin::isLoaded('Mailchimp')) {
            EventManager::instance()->on(new MailchimpService());
        }
    }
}
