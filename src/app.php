<?php
require_once __DIR__.'/bootstrap.php';

$app->get('/', function() use($app) { 
                    return 'Hello '; 
            });

$app->mount(
'/new',
new Bob\Route\NewPageControllerProvider()
);

$app->mount(
'/incoming',
new Bob\Route\SMSIncomingController()
);


$app->run(); 
