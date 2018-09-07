<?php
use Cake\Routing\Router;

Router::plugin(
    'Newsletter',
    ['path' => '/newsletter'],
    function ($routes) {
        $routes->connect('/subscribe/*', ['controller' => 'Newsletter', 'action' => 'subscribe']);
        $routes->connect('/unsubscribe/*', ['controller' => 'Newsletter', 'action' => 'unsubscribe']);
    }
);
