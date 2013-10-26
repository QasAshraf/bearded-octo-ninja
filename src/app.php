<?php
require_once __DIR__.'/bootstrap.php';

$app->get('/', function() use($app) {
    return $app['twig']->render('hello.twig', array(
        'name' => 'BON',
    ));
            });

$app->mount(
'/new',
new BON\Route\NewPageControllerProvider()
);

$app->mount(
'/incoming',
new BON\Route\SMSIncomingController()
);


$app->run(); 
