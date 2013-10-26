<?php
require_once __DIR__.'/bootstrap.php';

$app->get('/', function() use($app) {
	$grid = new BON\Model\MazeModel(21, 21);
    return $app['twig']->render('hello.twig', array(
        'grid' => json_encode($grid->get_grid()),
    ));
});

$app->get('/maze', function() use($app) {
	$grid = new BON\Model\MazeModel(31, 31);
    return json_encode($grid->get_grid());
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
