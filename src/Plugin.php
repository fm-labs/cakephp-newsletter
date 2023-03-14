<?php

namespace Newsletter;

use Cupcake\Plugin\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Newsletter\Service\MailchimpService;
use Newsletter\Service\NewsletterMailerService;

/**
 * Class NewsletterPlugin
 *
 * @package Newsletter
 */
class Plugin extends BasePlugin implements EventListenerInterface
{
    public function implementedEvents(): array
    {
        return [
            'Admin.Menu.build.admin_primary'    => ['callable' => 'buildAdminMenu', 'priority' => 80 ],
        ];
    }

    /**
     * @param Event $event
     * @return void
     */
    public function buildAdminMenu(Event $event)
    {
//        $event->getSubject()->addItem([
//            'title' => 'Newsletter',
//            'url' => ['plugin' => 'Newsletter', 'controller' => 'NewsletterMembers', 'action' => 'index'],
//            'data-icon' => 'newspaper-o',
////            'children' => [
////                'newsletter_lists' => [
////                    'title' => 'Newsletter Lists',
////                    'url' => ['plugin' => 'Newsletter', 'controller' => 'NewsletterLists', 'action' => 'index'],
////                    'data-icon' => 'list',
////                ],
////                'newsletter_members' => [
////                    'title' => 'Newsletter Members',
////                    'url' => ['plugin' => 'Newsletter', 'controller' => 'NewsletterMembers', 'action' => 'index'],
////                    'data-icon' => 'users',
////                ]
////            ]
//        ]);
    }

    public function bootstrap(PluginApplicationInterface $app): void
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
