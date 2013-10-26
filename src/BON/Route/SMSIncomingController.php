<?php
namespace BON\Route;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SMSIncomingController implements ControllerProviderInterface{

    public function connect(Application $app) 
    {
            $controllers = $app['controllers_factory'];

            $controllers->post('/', function(Request $request) 
	            { 
	            	
	            	$content = decodeContent($request->get('content'));

	                return new Response('Cheers Clockwork!', 200); 
	            }
	        ); 

        return $controllers;

    }

    protected function decodeContent($string)
    {
    	return explode(" ", urldecode($string), 2);
    }
}