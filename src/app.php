<?php
require_once __DIR__.'/bootstrap.php';

$app->get('/', function() use($app) {
	$grid = new BeardedOctoNinja\Model\Maze("The Crystal Maze", 21, 21);
    return $app['twig']->render('hello.twig', array(
        'grid' => json_encode($grid->get_grid()),
    ));
});

$app->run(); 
