<?php

namespace Newsletter;

use Backend\Backend;
use Backend\BackendPluginInterface;
use Banana\Application;
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
class NewsletterPlugin implements EventListenerInterface, PluginInterface, BackendPluginInterface
{
    public function implementedEvents()
    {
        return [
            'Backend.Sidebar.build'    => ['callable' => 'buildBackendMenu', 'priority' => 80 ],
        ];
    }

    /**
     * @param Event $event
     * @return void
     */
    public function buildBackendMenu(Event $event)
    {
        $event->subject()->addItem([
            'title' => 'Newsletter',
            'url' => ['plugin' => 'Newsletter', 'controller' => 'NewsletterMembers', 'action' => 'index'],
            'data-icon' => 'newspaper-o',
            'children' => [
                'newsletter_lists' => [
                    'title' => 'Newsletter Lists',
                    'url' => ['plugin' => 'Newsletter', 'controller' => 'NewsletterLists', 'action' => 'index'],
                    'data-icon' => 'list',
                ],
                'newsletter_members' => [
                    'title' => 'Newsletter Members',
                    'url' => ['plugin' => 'Newsletter', 'controller' => 'NewsletterMembers', 'action' => 'index'],
                    'data-icon' => 'users',
                ]
            ]
        ]);
    }

    public function bootstrap(Application $app)
    {
        EventManager::instance()->on($this);

        if (Configure::read('Newsletter.enableMailerService') == true) {
            EventManager::instance()->on(new NewsletterMailerService());
        }

        if (Configure::read('Newsletter.enableMailchimpService') == true && Plugin::loaded('Mailchimp')) {
            EventManager::instance()->on(new MailchimpService());
        }
    }

    public function routes(RouteBuilder $routes)
    {

    }

    public function middleware(MiddlewareQueue $middleware)
    {
    }

    public function backendBootstrap(Backend $backend)
    {
    }

    public function backendRoutes(RouteBuilder $routes)
    {
        $routes->fallbacks('DashedRoute');
    }
}
