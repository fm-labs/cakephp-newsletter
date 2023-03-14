<?php
/** @var \Cake\Routing\RouteBuilder $routes */
$routes->plugin(
    'Newsletter',
    ['path' => '/newsletter'],
    function ($routes) {
        $routes->connect('/subscribe/*', ['controller' => 'Newsletter', 'action' => 'subscribe']);
        $routes->connect('/unsubscribe/*', ['controller' => 'Newsletter', 'action' => 'unsubscribe']);
    }
);
